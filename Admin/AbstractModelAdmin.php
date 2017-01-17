<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 13/11/2016
 * Time: 21:06.
 */

namespace Mindy\Bundle\AdminBundle\Admin;

use Mindy\Bundle\AdminBundle\Admin\Event\AdminEvent;
use Mindy\Bundle\AdminBundle\Admin\Handler\OrderHandler;
use Mindy\Bundle\AdminBundle\Admin\Handler\SearchHandler;
use Mindy\Bundle\AdminBundle\Admin\Handler\SortHandler;
use Mindy\Bundle\AdminBundle\Form\DeleteConfirmForm;
use Mindy\Bundle\AdminBundle\Form\FilterFormInterface;
use Mindy\Bundle\AdminBundle\Form\Type\ButtonsType;
use Mindy\Orm\ModelInterface;
use Mindy\Orm\TreeManager;
use Mindy\Orm\TreeModel;
use Mindy\Orm\TreeQuerySet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class AbstractModelAdmin extends AbstractAdmin
{
    const EVENT_BEFORE_CREATE = 'before_create';
    const EVENT_AFTER_CREATE = 'after_create';
    const EVENT_BEFORE_UPDATE = 'before_update';
    const EVENT_AFTER_UPDATE = 'after_update';
    const EVENT_BEFORE_DELETE = 'before_update';
    const EVENT_AFTER_DELETE = 'after_update';

    /**
     * @var array
     */
    public $permissions = [
        'create' => true,
        'update' => true,
        'info' => true,
        'remove' => true,
    ];

    public $pager = [
        'pageSize' => 50,
    ];

    public $sorting = null;

    public $linkColumn = null;

    public $columns = null;

    public $defaultOrder = null;

    public $searchFields = null;

    protected $fetcher;

    protected $sortingHandler;

    protected $propertyAccessor;

    public function __construct()
    {
        $this->fetcher = new AdminValueFetcher();
        $this->propertyAccessor = new PropertyAccessor();
    }

    /**
     * @return FilterFormInterface|AbstractType
     */
    public function getFilterFormType()
    {
        return;
    }

    /**
     * @param ModelInterface $model
     *
     * @return array
     */
    public function getInfoFields(ModelInterface $model)
    {
        return $model->getMeta()->getAttributes();
    }

    /**
     * @param $code
     *
     * @return bool
     */
    public function can($code)
    {
        static $defaultPermissions = [
            'create' => true,
            'update' => true,
            'info' => true,
            'remove' => true,
        ];
        $permissions = array_merge($defaultPermissions, $this->permissions);

        return isset($permissions[$code]) && $permissions[$code];
    }

    abstract public function getFormType();

    abstract public function getModelClass();

    public function getVerboseNames()
    {
        return [];
    }

    /**
     * @param $column
     *
     * @return mixed
     */
    public function getVerboseName($column)
    {
        $model = (new \ReflectionClass($this->getModelClass()))->newInstance();
        $names = $this->getVerboseNames();
        if (isset($names[$column])) {
            return $names[$column];
        } elseif ($model->hasField($column)) {
            return $model->getField($column)->getVerboseName();
        } else {
            return $column;
        }
    }

    /**
     * Render table cell. Used in template list.html.
     *
     * @param $column
     * @param ModelInterface $model
     *
     * @return string
     */
    public function renderCell($column, ModelInterface $model)
    {
        $value = $this->fetcher->fetchValue($column, $model);

        if ($template = $this->findTemplate(sprintf('columns/_%s.html', $column), false)) {
            return $this->renderTemplate($template, [
                'admin' => $this,
                'model' => $model,
                'column' => $column,
                'value' => $value,
            ]);
        } else {
            return $value;
        }
    }

    public function getColumns()
    {
        if (null === $this->columns) {
            $fields = call_user_func([$this->getModelClass(), 'getMeta'])->getFields();

            return array_keys($fields);
        }

        return $this->columns;
    }

    /**
     * Get qs from model.
     *
     * @return \Mindy\Orm\Manager|\Mindy\Orm\TreeManager
     */
    public function getQuerySet()
    {
        return call_user_func([$this->getModelClass(), 'objects']);
    }

    public function listAction(Request $request)
    {
        $qs = $this->getQuerySet();
        $tree = $qs instanceof TreeManager || $qs instanceof TreeQuerySet;

        $view = $this->get('admin.view.list');

        if ($request->isXmlHttpRequest()) {
            $view->setTemplate($this->findTemplate('_table.html'));
        } else {
            $view->setTemplate($this->findTemplate('list.html'));
        }

        // TODO
        if ($tree) {
            if ($request->query->has('parent_id') && ($pk = $request->query->getInt('parent_id'))) {
                $clone = clone $qs;
                $parent = $clone->get(['pk' => $pk]);
                $qs->filter(['parent_id' => $pk]);

                if (null === $parent) {
                    throw new NotFoundHttpException();
                }
            } else {
                $qs->roots();
            }
        }

        $view->setQuerySet($qs);
        $view->setPaginationParameters($this->pager);

        $view->setSearchHandler(new SearchHandler($request, 'search', $this->searchFields));
        $view->setOrderHandler(new OrderHandler($request, 'order', $this->defaultOrder));

        if ($this->sorting) {
            $view->setSortHandler(new SortHandler($request, 'models', $this->sorting, $this->sorting));
        }

        $view->handleRequest($request);

        $instance = (new \ReflectionClass($this->getModelClass()))->newInstance();

        return $view->render([
            'admin' => $this,
            'tree' => $tree,
            'breadcrumbs' => $this->fetchBreadcrumbs($request, $instance, 'list'),
            'linkColumn' => $this->linkColumn,
            'columns' => $this->getColumns(),
        ]);
    }

    /**
     * @param Request $request
     * @param ModelInterface $model
     * @param string $action
     *
     * @return array
     */
    public function getCustomBreadrumbs(Request $request, ModelInterface $model, $action)
    {
        if ($model instanceof TreeModel) {
            $pk = $request->query->get('pk');
            if (!empty($pk)) {
                /** @var null|TreeModel $instance */
                $instance = call_user_func([$this->getModelClass(), 'objects'])->get(['pk' => $pk]);
                if ($instance) {
                    return $this->getParentBreadcrumbs($instance);
                }
            }
        }

        return [];
    }

    /**
     * @param $model
     *
     * @return array
     */
    public function getParentBreadcrumbs(TreeModel $model)
    {
        $parents = [];

        if ($model->pk) {
            $parents = $model->objects()->ancestors()->order(['lft'])->all();
            $parents[] = $model;
        }

        $breadcrumbs = [];
        foreach ($parents as $parent) {
            $breadcrumbs[] = [
                'url' => $this->getAdminUrl('list', ['parent_id' => $parent->pk]),
                'name' => (string)$parent,
                'items' => [],
            ];
        }

        return $breadcrumbs;
    }

    /**
     * @param Request $request
     * @param ModelInterface $model
     * @param $action
     *
     * @return array
     */
    public function fetchBreadcrumbs(Request $request, ModelInterface $model, $action)
    {
        list($list, $create, $update) = $this->getAdminNames($model);
        $breadcrumbs = [
            ['name' => $list, 'url' => $this->getAdminUrl('list')],
        ];
        $custom = $this->getCustomBreadrumbs($request, $model, $action);
        if (!empty($custom)) {
            // Fetch user custom breadcrumbs
            $breadcrumbs = array_merge($breadcrumbs, $custom);
        }

        $bundleName = $this->getBundle()->getName();
        switch ($action) {
            case 'create':
                $breadcrumbs[] = ['name' => $create];
                break;
            case 'update':
                $breadcrumbs[] = ['name' => $update];
                break;
            case 'list':
                break;
            case 'info':
                $breadcrumbs[] = [
                    'name' => $this->get('translator')->trans('admin.breadcrumbs.info', ['%name%' => (string)$model], sprintf('%s.admin', $bundleName)),
                    'url' => $this->getAdminUrl('list'),
                ];
                break;
            default:
                break;
        }

        return $breadcrumbs;
    }

    /**
     * Array of action => name, where actions is an
     * action in this admin class.
     *
     * @return array
     */
    public function getActions()
    {
        return $this->can('remove') ? [
            'batchRemove' => $this->get('translator')->trans('admin.actions.batch_remove'),
        ] : [];
    }

    /**
     * @param ModelInterface|null $instance
     *
     * @return array
     */
    public function getAdminNames(ModelInterface $instance = null)
    {
        $bundleName = strtolower(str_replace('Bundle', '', $this->getBundle()->getName()));
        $model = str_replace(' ', '_', TextHelper::normalizeName(TextHelper::shortName($this->getModelClass())));
        $trans = $this->get('translator');

        return [
            $trans->trans(sprintf('%s.admin.%s.list', $bundleName, $model)),
            $trans->trans(sprintf('%s.admin.%s.create', $bundleName, $model)),
            $trans->trans(sprintf('%s.admin.%s.update', $bundleName, $model), ['%name%' => (string)$instance]),
        ];
    }

    /**
     * TODO
     * @param Request $request
     * @return string
     */
    public function infoAction(Request $request)
    {
        $instance = call_user_func([$this->getModelClass(), 'objects'])->get([
            'pk' => $request->query->get('pk'),
        ]);

        if (null === $instance) {
            throw new NotFoundHttpException();
        }

        $fields = [];
        foreach ($this->getInfoFields($instance) as $fieldName) {
            $fields[$fieldName] = $instance->getField($fieldName);
        }

        return $this->render($this->findTemplate('info.html'), [
            'model' => $instance,
            'fields' => $fields,
            'breadcrumbs' => $this->fetchBreadcrumbs($request, $instance, 'info'),
        ]);
    }

    public function printAction(Request $request)
    {
        $instance = call_user_func([$this->getModelClass(), 'objects'])->get([
            'pk' => $request->query->get('pk'),
        ]);

        if (null === $instance) {
            throw new NotFoundHttpException();
        }

        $fields = [];
        foreach ($this->getInfoFields($instance) as $fieldName) {
            $fields[$fieldName] = $instance->getField($fieldName);
        }

        return $this->render($this->findTemplate('info_print.html'), [
            'model' => $instance,
            'fields' => $fields,
            'breadcrumbs' => $this->fetchBreadcrumbs($request, $instance, 'info'),
        ]);
    }

    protected function getDeleteFormType()
    {
        return DeleteConfirmForm::class;
    }

    public function createAction(Request $request)
    {
        $instance = (new \ReflectionClass($this->getModelClass()))->newInstance();

        $view = $this->get('admin.view.create');
        $view->setTemplate($this->findTemplate('create.html'));
        $view->setForm($this->getFormType());
        $view->setModel($instance);

        if ($view->handleRequest($request)->isSubmitted()) {
            $form = $view->getForm();
            $instance = $form->getData();
            $this->getEventDispatcher()->dispatch(self::EVENT_BEFORE_CREATE, new AdminEvent($instance));

            if ($instance->save()) {
                $this->getEventDispatcher()->dispatch(self::EVENT_AFTER_CREATE, new AdminEvent($instance));

                $this->addFlash(self::FLASH_SUCCESS, $this->get('translator')->trans('admin.flash.success'));
                return $this->getNextRoute($form->get('buttons'), $instance);
            }
        }

        return $view->render([
            'admin' => $this,
            'breadcrumbs' => $this->fetchBreadcrumbs($request, $instance, 'create'),
        ]);
    }

    /**
     * Example usage:.
     *
     * switch ($action) {
     *      case "save_create":
     *          return ['parent' => 'parent_id'];
     *      case "save":
     *          return ['parent' => 'pk'];
     *      default:
     *          return [];
     * }
     *
     * @param $action
     *
     * @return array
     */
    public function getRedirectParams($action)
    {
        return [];
    }

    /**
     * Collect correct array for redirect.
     *
     * @param array $attributes
     * @param $action
     *
     * @return array
     */
    protected function fetchRedirectParams(array $attributes, $action)
    {
        $redirectParams = [];
        $saveParams = $this->getRedirectParams($action);
        foreach ($attributes as $key => $value) {
            if (array_key_exists($key, $saveParams)) {
                $redirectParams[$saveParams[$key]] = $value;
            }
        }

        return $redirectParams;
    }

    /**
     * @param FormInterface|ButtonsType $buttons
     * @param ModelInterface $instance
     * @return string url for redirect
     */
    public function getNextRoute(FormInterface $buttons, ModelInterface $instance)
    {
        if ($buttons->get('save')->isClicked()) {
            return $this->redirect($this->getAdminUrl('update', ['pk' => $instance->pk]));
        } elseif ($buttons->get('save_and_return')->isClicked()) {
            return $this->redirect($this->getAdminUrl('list'));
        } elseif ($buttons->get('save_and_create')->isClicked()) {
            return $this->redirect($this->getAdminUrl('create'));
        }
    }

    public function updateAction(Request $request)
    {
        $instance = call_user_func([$this->getModelClass(), 'objects'])->get([
            'pk' => $request->query->get('pk'),
        ]);
        if ($instance === null) {
            throw new NotFoundHttpException();
        }

        $view = $this->get('admin.view.update');
        $view->setTemplate($this->findTemplate('update.html'));
        $view->setForm($this->getFormType());
        $view->setModel($instance);

        if ($view->handleRequest($request)->isSubmitted()) {
            $form = $view->getForm();
            $instance = $form->getData();
            $this->getEventDispatcher()->dispatch(self::EVENT_BEFORE_UPDATE, new AdminEvent($instance));

            if ($instance->save()) {
                $this->getEventDispatcher()->dispatch(self::EVENT_BEFORE_UPDATE, new AdminEvent($instance));

                $this->addFlash(self::FLASH_SUCCESS, $this->get('translator')->trans('admin.flash.success'));

                return $this->getNextRoute($form->get('buttons'), $instance);
            }
        }

        return $view->render([
            'admin' => $this,
            'breadcrumbs' => $this->fetchBreadcrumbs($request, $instance, 'update'),
        ]);
    }

    public function removeAction(Request $request)
    {
        /** @var ModelInterface $instance */
        $instance = call_user_func([$this->getModelClass(), 'objects'])->get([
            'pk' => $request->query->get('pk'),
        ]);
        if ($instance === null) {
            throw new NotFoundHttpException();
        }

        $view = $this->get('admin.view.delete');
        $view->setTemplate($this->findTemplate('delete.html'));
        $view->setForm($this->getDeleteFormType());

        if ($view->handleRequest($request)->isSubmitted()) {
            if ($view->getForm()->isValid()) {
                $this->getEventDispatcher()->dispatch(self::EVENT_BEFORE_DELETE, new AdminEvent($instance));

                if ($instance->delete()) {
                    $this->getEventDispatcher()->dispatch(self::EVENT_AFTER_DELETE, new AdminEvent($instance));

                    $this->addFlash(self::FLASH_SUCCESS, $this->get('translator')->trans('admin.flash.success'));
                    return $this->redirect($this->getAdminUrl('list'));
                }
            }
        }

        return $view->render([
            'admin' => $this
        ]);
    }

    protected function getBundle()
    {
        $name = call_user_func([$this->getModelClass(), 'getBundleName']);
        return $this->get('kernel')->getBundle($name);
    }
}
