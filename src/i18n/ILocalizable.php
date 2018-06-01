<?php
namespace onix\i18n;

/**
 * Interface ILocalizable
 * Defines the signature for a component that has a localized name.
 *
 */
interface ILocalizable
{
    /**
     * Get localized name
     * @return string
     */
    public function getLocalizedName();
}
