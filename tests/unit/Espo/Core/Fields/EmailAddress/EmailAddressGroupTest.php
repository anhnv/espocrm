<?php
/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2021 Yurii Kuznietsov, Taras Machyshyn, Oleksii Avramenko
 * Website: https://www.espocrm.com
 *
 * EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 ************************************************************************/

namespace tests\unit\Espo\Core\Fields\Address;

use Espo\Core\{
    Fields\EmailAddress\EmailAddress,
    Fields\EmailAddress\EmailAddressGroup,
};

class EmailAddressGroupTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp() : void
    {

    }

    public function testEmpty()
    {
        $group = EmailAddressGroup::fromNothing();

        $this->assertEquals(0, count($group->getAddressList()));
        $this->assertEquals(0, count($group->getSecondaryList()));

        $this->assertNull($group->getPrimary());
    }

    public function testWithPrimary1()
    {
        $primary = EmailAddress::fromAddress('primary@test.com')->invalid();

        $group = EmailAddressGroup::fromNothing()->withPrimary($primary);

        $this->assertEquals(1, count($group->getList()));
        $this->assertEquals(0, count($group->getSecondaryList()));

        $this->assertNotNull($group->getPrimary());

        $this->assertEquals('primary@test.com', $group->getPrimary()->getAddress());

        $primaryAnother = EmailAddress::fromAddress('primaryAnother@test.com');

        $groupAnother = $group->withPrimary($primaryAnother);

        $this->assertEquals(2, count($groupAnother->getList()));
        $this->assertEquals(1, count($groupAnother->getSecondaryList()));

        $this->assertEquals('primaryAnother@test.com', $groupAnother->getPrimary()->getAddress());


        $this->assertEquals('primary@test.com', $groupAnother->getList()[1]->getAddress());
        $this->assertTrue($groupAnother->getList()[1]->isInvalid());

        $this->assertTrue($groupAnother->hasAddress('primary@test.com'));
        $this->assertTrue($groupAnother->hasAddress('primaryAnother@test.com'));
    }

    public function testWithPrimary2()
    {

        $address = EmailAddress::fromAddress('one@test.com')->invalid();

        $group = EmailAddressGroup::fromList([$address])
            ->withAdded(
                EmailAddress::fromAddress('two@test.com')
            )
            ->withPrimary(
                EmailAddress::fromAddress('two@test.com')
            );


        $this->assertEquals('two@test.com', $group->getPrimary()->getAddress());

        $this->assertEquals(2, count($group->getList()));
    }

    public function testWithAdded1()
    {

        $address = EmailAddress::fromAddress('one@test.com')->invalid();

        $group = EmailAddressGroup::fromList([$address])
            ->withAdded(
                EmailAddress::fromAddress('two@test.com')
            );


        $this->assertEquals('one@test.com', $group->getPrimary()->getAddress());

        $this->assertEquals(2, count($group->getList()));
    }


}
