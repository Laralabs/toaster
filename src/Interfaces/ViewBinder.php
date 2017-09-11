<?php

namespace Laralabs\Toaster\Interfaces;

interface ViewBinder
{
    /**
     * Bind JavaScript variables to the view.
     *
     * @param $js
     *
     * @return mixed
     */
    public function bind($js);
}
