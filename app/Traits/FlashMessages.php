<?php

namespace App\Traits;

trait FlashMessages
{
    /**
     * Flash a success message
     */
    protected function flashSuccess(string $message): void
    {
        session()->flash('success', $message);
    }

    /**
     * Flash an error message
     */
    protected function flashError(string $message): void
    {
        session()->flash('error', $message);
    }

    /**
     * Flash a warning message
     */
    protected function flashWarning(string $message): void
    {
        session()->flash('warning', $message);
    }

    /**
     * Flash an info message
     */
    protected function flashInfo(string $message): void
    {
        session()->flash('info', $message);
    }

    /**
     * Flash a message with custom type
     */
    protected function flashMessage(string $type, string $message): void
    {
        session()->flash($type, $message);
    }
}
