<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Método para almacenar imágenes
     */
    public function postFileUploadCdn(
        $field,
        $suggestion_name,
        $request,
        $thumbnails = null
    ) {
        $path = date('Y/m/d');

        $file = $request->file($field);

        if (! $file) {
            return json_encode([
                'upload' => 'error',
                'message' => 'No se recibió ningún archivo.',
            ]);
        }

        $original_name = $file->getClientOriginalName();

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        /*
         * NOMBRE DEL ARCHIVO FINAL
         */
        $final_name = $suggestion_name
            ? Str::slug($suggestion_name).'_'.time().'.'.$extension
            : Str::slug(
                pathinfo(
                    $original_name,
                    PATHINFO_FILENAME
                )
            ).'_'.time().'.'.$extension;

        /*
         * Guardar archivo en el disco CDN
         */
        if (! $file->storeAs($path, $final_name, 'cdn')) {
            return json_encode([
                'upload' => 'error',
                'message' => 'No se pudo guardar el archivo.',
            ]);
        }

        $data = json_encode([
            'upload' => 'success',
            'path' => $path,
            'original_name' => $original_name,
            'final_name' => $final_name,
        ]);

        /*
         * Generar miniaturas
         */
        if (
            in_array(
                $extension,
                [
                    'jpg',
                    'jpeg',
                    'png',
                    'gif',
                    'bmp',
                    'webp',
                ]
            )
            && $thumbnails
        ) {

            $file_path = config('filesystems.disks.cdn.root')
                .DIRECTORY_SEPARATOR
                .$path
                .DIRECTORY_SEPARATOR
                .$final_name;

            /*
             * Crear Image Manager
             */
            $manager = new ImageManager(
                new Driver
            );

            /*
             * Crear miniaturas
             */
            foreach ($thumbnails as $key) {

                /*
                 * Validar estructura del thumbnail
                 *
                 * $key[0] = ancho
                 * $key[1] = alto
                 * $key[2] = nombre/prefijo
                 */
                if (
                    ! isset($key[0]) ||
                    ! isset($key[1]) ||
                    ! isset($key[2])
                ) {
                    continue;
                }

                /*
                 * Leer imagen
                 */
                $img = $manager->decodePath($file_path);

                /*
                 * Ajustar tamaño manteniendo proporción
                 */
                $img->cover(
                    $key[0],
                    $key[1]
                );

                /*
                 * Ruta de la miniatura
                 */
                $thumbnail_path =
                    config('filesystems.disks.cdn.root')
                    .DIRECTORY_SEPARATOR
                    .$path
                    .DIRECTORY_SEPARATOR
                    .$key[2]
                    .'_'
                    .$final_name;

                /*
                 * Guardar miniatura
                 */
                $img->save($thumbnail_path);
            }
        }

        return $data;
    }
}
