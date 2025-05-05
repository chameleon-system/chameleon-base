<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\NewsletterBundle\Test;

use ChameleonSystem\NewsletterBundle\PostProcessing\Bridge\NewsletterUserDataModel;
use ChameleonSystem\NewsletterBundle\PostProcessing\PostProcessorUserContent;
use PHPUnit\Framework\TestCase;

class PostProcessorUserContentTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider getUserData
     */
    public function itSubstitutesCorrectly($salutation, $firstName, $lastName, $email, $unsubLink, $htmlLink)
    {
        $mockUserData = new NewsletterUserDataModel($salutation, $firstName, $lastName, $email, $unsubLink, $htmlLink);

        $processor = new PostProcessorUserContent();

        $this->assertEquals(join(', ', [$salutation, $firstName, $lastName, $email, $unsubLink, $htmlLink]), $processor->process('[{salutation}], [{firstname}], [{lastname}], [{email}], [{unsubscribelink}], [{htmllink}]', $mockUserData));
    }

    /**
     * @test
     *
     * @dataProvider getUserData
     */
    public function itLeavesTextWithoutPlaceholdersUnchanged($salutation, $firstName, $lastName, $email, $unsubLink, $htmlLink)
    {
        $mockUserData = new NewsletterUserDataModel($salutation, $firstName, $lastName, $email, $unsubLink, $htmlLink);

        $processor = new PostProcessorUserContent();

        $this->assertEquals('foo', $processor->process('foo', $mockUserData));
    }

    public function getUserData()
    {
        return [
            ['Mister', 'Dev', 'Eloper', 'dev.eloper@example.com', 'http://unsubscribe', 'http://html'],
        ];
    }
}
