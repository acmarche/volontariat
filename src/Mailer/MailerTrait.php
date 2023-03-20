<?php

namespace AcMarche\Volontariat\Mailer;

trait MailerTrait
{

    private function defaultParams(): array
    {
        return [
            'importance' => 'high',
            'content' => '',
            'action_url' => '',
            'action_text' => '',
            'footer_text' => '',
            'markdown' => false,
            'raw' => false,
            'exception' => false,
        ];
    }
}