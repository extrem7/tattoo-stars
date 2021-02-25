<?php

namespace App\Models\Traits;

use Spatie\Translatable\HasTranslations as BaseHasTranslations;

trait HasTranslations
{
    use BaseHasTranslations {
        getAttributeValue as getAttributeTranslatedValue;
    }

    protected bool $translateAttributes = false;

    public function translated(): self
    {
        $this->translateAttributes = true;
        return $this;
    }

    public function getAttributeValue($key)
    {
        if ($this->translateAttributes) {
            return $this->getAttributeTranslatedValue($key);
        }
        return parent::getAttributeValue($key);
    }

    public function toArray(): array
    {
        $attributes = parent::toArray();
        if ($this->translateAttributes) {
            foreach ($this->getTranslatableAttributes() as $field) {
                $attributes[$field] = $this->getTranslation($field, \App::getLocale());
            }
        }
        return $attributes;
    }
}
