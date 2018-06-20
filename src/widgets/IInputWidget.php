<?php
namespace onix\widgets;

interface IInputWidget
{
    /**
     * Initializes the widget.
     * If you override this method, make sure you call the parent implementation first.
     */
    public function init();
}
