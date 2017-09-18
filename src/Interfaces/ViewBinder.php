<?php

namespace Laralabs\Toaster\Interfaces;

interface ViewBinder
{
    /**
     * Generates a JS variable.
     *
     * @return mixed
     */
    public function generateJs();

    /**
     * Bind JavaScript variables to the view.
     *
     * @return mixed
     */
    public function bind();
}
