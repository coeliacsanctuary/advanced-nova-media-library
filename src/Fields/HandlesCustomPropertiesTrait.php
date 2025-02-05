<?php

namespace Jpeters8889\AdvancedNovaMediaLibrary\Fields;

use Laravel\Nova\Fields\Field;
use Spatie\MediaLibrary\HasMedia;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media as MediaModel;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @mixin Media
 */
trait HandlesCustomPropertiesTrait
{
    protected $customPropertiesFields = [];
    protected $customProperties = [];

    public function customPropertiesFields(array $customPropertiesFields): self
    {
        $this->customPropertiesFields = collect($customPropertiesFields);

        return $this->withMeta(compact('customPropertiesFields'));
    }

    public function customProperties(array $customProperties): self
    {
        $this->customProperties = $customProperties;

        return $this;
    }

    private function fillCustomPropertiesFromRequest(NovaRequest $request, string $requestAttribute, HasMedia $model, string $collection)
    {
        $key = str_replace($collection, '__media__.'.$collection, $requestAttribute);

        $mediaItems = $model->getMedia($collection);
        $items = $request->input($key, []);

        // do not handle files as custom properties on files are not supported yet
        if ($items instanceof FileBag) {
            return;
        }

        collect($items)
            ->reject(function ($value) {
                return $value instanceof UploadedFile || $value instanceof FileBag;
            })
            ->each(function ($id, int $index) use ($request, $mediaItems, $collection, $requestAttribute) {
                if (! $media = $mediaItems->where('id', $id)->first()) {
                    return;
                }

                $this->fillMediaCustomPropertiesFromRequest($request, $media, $index, $collection, $requestAttribute);
            });
    }

    /**
     * @param MediaModel $media
     */
    private function fillMediaCustomPropertiesFromRequest(NovaRequest $request, MediaModel $media, int $index, string $collection, string $requestAttribute)
    {
        // prevent overriding the custom properties set by other processes like generating convesions
        $media->refresh();

        /** @var Field $field */
        foreach ($this->customPropertiesFields as $field) {
            $key = str_replace($collection, '__media-custom-properties__.'.$collection, $requestAttribute);
            $targetAttribute = "custom_properties->{$field->attribute}";
            $requestAttribute = "__media-custom-properties__.{$collection}.{$index}.{$field->attribute}";
            $targetRequestAttribute = "{$key}.{$index}.{$field->attribute}";

            $field->fillInto($request, $media, $targetAttribute, $targetRequestAttribute);
        }

        $media->save();
    }
}
