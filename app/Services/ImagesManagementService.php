<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImagesManagementService
{
    public function addImages($request, $model, $column_name): void
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                //enregistre les images dans le dossier storage/app/public/images et l'url pour y accéder dans la table image
                $imagePath = $image->store('images', 'public');
                $image = new Image([
                    'url' => url('storage/' . $imagePath),
                    $column_name => $model->id,
                ]);
                $image->save();
            }
        }
    }

    public function updateImages($request, $model, $column_name): void
    {
        if ($request->hasFile('images')) {
            $existingImages = $model->images()->get();

            //supprime les images du strage et l'url de la table images
            if ($existingImages) {
                foreach ($existingImages as $existingImage) {
                    Storage::disk('public')->delete($existingImage->url);
                    $existingImage->delete();
                }
            }

            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('images', 'public');
                $image = new Image([
                    'url' => url('storage/' . $imagePath),
                    $column_name => $model->id,
                ]);
                $image->save();
            }
        }
    }

    public function deleteImages($model): void
    {
        $existingImages = $model->images()->get();

        if ($existingImages) {
            foreach ($existingImages as $existingImage) {
                Storage::disk('public')->delete($existingImage->url);
                $existingImage->delete();
            }
        }
    }

    public function addSingleImage($request, $model, $column_name): void
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            //enregistre les images dans le dossier storage/app/public/images et l'url pour y accéder dans la table image
            $imagePath = $image->store('images', 'public');
            $image = new Image([
                'url' => url('storage/' . $imagePath),
                $column_name => $model->id,
            ]);
            $image->save();
        }
    }


    public function updateSingleImage($request, $model, $column_name): void
    {
        if ($request->hasFile('image')) {
            $existingImage = $model->images()->first();

            //supprime les images du strage et l'url de la table images
            if ($existingImage) {
                Storage::disk('public')->delete($existingImage->url);
                $existingImage->delete();
            }
            $image = $request->file('image');
            $imagePath = $image->store('images', 'public');
            $image = new Image([
                'url' => url('storage/' . $imagePath),
                $column_name => $model->id,
            ]);
            $image->save();
        }
    }

    public function deleteSingleImage($model): void
    {
        $existingImage = $model->images()->first();
        if ($existingImage) {
            Storage::disk('public')->delete($existingImage->url);
            $existingImage->delete();
        }
    }
}
