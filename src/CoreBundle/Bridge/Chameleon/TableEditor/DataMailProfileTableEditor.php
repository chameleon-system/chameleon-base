<?php
namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\TableEditor;


use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Psr\Log\LoggerInterface;
use TCMSTableEditor;
use Twig\Error\SyntaxError;
use Twig\Environment;
use Twig\Source;

class DataMailProfileTableEditor extends TCMSTableEditor
{
    const MESSAGE_MANAGER_CONSUMER = 'DataMailProfileTableEditorMessages';

    protected function DataIsValid($postData, $oFields = null)
    {
        $twig = $this->getTwigEnvironment();
        try {
            $twig->tokenize(new Source($postData['body'], 'body'));
        }catch (SyntaxError $e) {

            $this->getLogger()->error(
                sprintf('failed to parse body field in E-Mail Template %s', $postData['name']),
                [
                    'sFieldName' => 'body',
                    'message' => $e->getMessage(),
                    'guess' => $e->guess(),
                    'context' => $e->getSourceContext()->getCode(),
                    'exception' => $e,
                ]
            );

            $this->getFlashMessageService()->addMessage(
                self::MESSAGE_MANAGER_CONSUMER,
                'chameleon_system.table_editor_twig_body_parse_error',
                [
                    'sFieldName' => 'body',
                    'message' => $e->getMessage(),
                    'guess' => $e->guess(),
                ]
            );
            return false;
        }


        try {
            $twig->tokenize(new Source($postData['body_text'], 'body_text'));
        }catch (SyntaxError $e) {

            $this->getLogger()->error(
                sprintf('failed to parse body_text field in E-Mail Template %s', $postData['name']),
                [
                    'sFieldName' => 'body_text',
                    'message' => $e->getMessage(),
                    'guess' => $e->guess(),
                    'context' => $e->getSourceContext()->getCode(),
                    'exception' => $e,
                ]
            );

            $this->getFlashMessageService()->addMessage(
                self::MESSAGE_MANAGER_CONSUMER,
                'chameleon_system.table_editor_twig_body_text_parse_error',
                [
                    'sFieldName' => 'body_text',
                    'message' => $e->getMessage(),
                    'guess' => $e->guess(),
                ]
            );
            return false;
        }

        return parent::DataIsValid($postData, $oFields);
    }

    private function getFlashMessageService(): FlashMessageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.flash_messages');
    }

    private function getTwigEnvironment(): Environment
    {
        return ServiceLocator::get('twig');
    }

    private function getLogger(): LoggerInterface
    {
        return ServiceLocator::get('logger');
    }
}
