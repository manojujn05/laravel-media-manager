<?php

namespace Innopanda\AssetManager\Traits;

trait WithConfirmation
{
    public bool $showConfirmationModal = false;
    public string $confirmationModalTitle = '';
    public string $confirmationModalMessage = '';
    public string $confirmationModalConfirmText = 'Confirm';
    public string $confirmationModalType = 'destructive';
    
    public string $confirmationAction = '';
    public mixed $confirmationPayload = null;

    public function confirmAction($title, $message, $action, $payload = null, $confirmText = 'Confirm', $type = 'destructive')
    {
        $this->confirmationModalTitle = $title;
        $this->confirmationModalMessage = $message;
        $this->confirmationAction = $action;
        $this->confirmationPayload = $payload;
        $this->confirmationModalConfirmText = $confirmText;
        $this->confirmationModalType = $type;
        $this->showConfirmationModal = true;
    }

    public function cancelConfirmation()
    {
        $this->showConfirmationModal = false;
        $this->confirmationAction = '';
        $this->confirmationPayload = null;
    }

    public function executeConfirmation()
    {
        if ($this->confirmationAction) {
            $action = $this->confirmationAction;
            $payload = $this->confirmationPayload;

            $this->cancelConfirmation();

            if (method_exists($this, $action)) {
                if ($payload !== null) {
                    $this->{$action}($payload);
                } else {
                    $this->{$action}();
                }
            }
        }
    }
}
