<?php

/*
 * This file is part of the Active Collab Controller project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Controller\Test;

use ActiveCollab\Controller\ActionResultEncoder\ActionResultEncoder;
use ActiveCollab\Controller\Test\Base\TestCase;

class ActionResultEncoderTest extends TestCase
{
    public function testDefaultActionResultAttribute()
    {
        $this->assertSame('action_result', (new ActionResultEncoder())->getRequestAttributeName());
    }

    public function testDefaultActionResultAttributeNameCantBeEmpty()
    {
        (new ActionResultEncoder())->setRequestAttributeName('');
    }

    public function testActionResultAttributeCanBeChanged()
    {
        $this->assertSame('change_attribute_name', (new ActionResultEncoder())->setRequestAttributeName('change_attribute_name')->getRequestAttributeName());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Request attribute 'action_result' not found.
     */
    public function testExceptionWhenActionResultIsNotFoundInRequest()
    {
        call_user_func(new ActionResultEncoder(), $this->createRequest(), $this->createResponse());
    }
}
