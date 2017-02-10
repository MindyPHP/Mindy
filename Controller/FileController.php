<?php

/*
 * (c) Studio107 <mail@studio107.ru> http://studio107.ru
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Author: Maxim Falaleev <max@studio107.ru>
 */

namespace Mindy\Bundle\FileBundle\Controller;

use League\Flysystem\FilesystemInterface;
use Mindy\Bundle\MindyBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    const UPLOAD_NAME = 'files';

    /**
     * @return FilesystemInterface
     */
    protected function getFilesystem()
    {
        return $this->get($this->getParameter('file.filesystem'));
    }

    public function createDirectoryAction(Request $request)
    {
        $path = $request->query->get('path', '/');
        $directoryName = $request->query->get('directory');

        if (empty($directoryName)) {
            return $this->json([
                'status' => false,
                'message' => $this->get('translator')->trans('file.directory.missing_name_error'),
            ]);
        } elseif (strpos($directoryName, '/') !== false) {
            return $this->json([
                'status' => false,
                'message' => $this->get('translator')->trans('file.directory.incorrect_name_error'),
            ]);
        }
        $fs = $this->getFilesystem();
        $dirPath = implode('/', [$path, $directoryName]);

        if ($fs->has($dirPath)) {
            return $this->json([
                'status' => false,
                'message' => $this->get('translator')->trans('file.directory.exist_error'),
            ]);
        }
        if ($fs->createDir($dirPath)) {
            return $this->json([
                'status' => true,
                'message' => $this->get('translator')->trans('file.directory.create_success'),
            ]);
        }

        return $this->json([
            'status' => true,
            'message' => $this->get('translator')->trans('file.directory.create_error'),
        ]);
    }

    public function listAction(Request $request)
    {
        $path = urldecode($request->query->get('path', '/'));

        $objects = [];
        foreach ($this->getFilesystem()->listContents($path) as $object) {
            $objects[] = [
                'path' => '/' . $object['path'],
                'name' => basename($object['path']),
                'date' => isset($object['timestamp']) ? date(DATE_W3C, $object['timestamp']) : null,
                'is_dir' => $object['type'] === 'dir',
                'size' => isset($object['size']) ? $object['size'] : 0,
                'url' => $object['path'],
            ];
        }

        $breadcrumbs = [
            [
                'url' => $this->generateUrl('file_list'),
                'name' => $this->get('translator')->trans('admin.file.name'),
            ],
        ];
        $prev = [];
        foreach (array_filter(explode('/', $path)) as $part) {
            $prev[] = $part;

            $query = ['path' => '/' . implode('/', $prev)];
            $url = $this->generateUrl('file_list', $query);
            $breadcrumbs[] = ['url' => $url, 'name' => $part];
        }

        return $this->render('file/list.html', [
            'breadcrumbs' => $breadcrumbs,
            'objects' => $objects,
        ]);
    }

    public function deleteAction(Request $request)
    {
        $path = $request->query->get('path', '/');
        $fs = $this->getFilesystem();
        if ($fs->has($path)) {
            $meta = $fs->getMetadata($path);
            if ($meta['type'] === 'file') {
                $fs->delete($path);
            } else {
                $fs->deleteDir($path);
            }

            return $this->json(['status' => true]);
        }

        return $this->json(['status' => false, 'error' => 'Path not found']);
    }

    public function uploadAction(Request $request)
    {
        $path = $request->query->get('path', '/');

        $filesystem = $this->getFilesystem();
        $files = $request->files->get(self::UPLOAD_NAME);
        foreach ($files as $file) {
            /** @var UploadedFile $file */
            if ($file->isValid()) {
                $stream = fopen($file->getRealPath(), 'r+');
                $filesystem->writeStream(sprintf('%s/%s', $path, $file->getClientOriginalName()), $stream);
                fclose($stream);
            }
        }
        return new Response('');
    }
}
