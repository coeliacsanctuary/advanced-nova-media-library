<?php

namespace Jpeters8889\AdvancedNovaMediaLibrary\Fields;

class Images extends Media
{
    protected $defaultValidatorRules = ['image'];

    /**
     * Do we deprecate this for SingleMediaRules?
     * @param $singleImageRules
     * @return Images
     */
    public function singleImageRules($singleImageRules): self
    {
        $this->singleMediaRules = $singleImageRules;

        return $this;
    }

    public function addButtonLabel(string $label): self
    {
        return $this->withMeta(['addLabel' => $label]);
    }

    public function insertable(string $field = 'body'): self
    {
        $this->mustUpload(true);

        return $this->withMeta(['insertable' => $field]);
    }

    public function mustUpload(bool $mustUpload): self
    {
        return $this->withMeta(['mustUpload' => $mustUpload]);
    }
}
