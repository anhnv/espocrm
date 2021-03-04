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

namespace Espo\Core\Fields\EmailAddress;

use RuntimeException;

/**
 * An email address group. Contains a list of email addresses with a primary one.
 * If not empty, then there always should be a primary address.
 * Immutable.
 */
class EmailAddressGroup
{
    /**
     * @var array<EmailAddress>
     */
    private $list = [];

    /**
     * @var ?EmailAddress
     */
    private $primary = null;

    /**
     * @param array<EmailAddress> $list
     */
    private function __construct(array $list)
    {
        $this->list = $list;

        $this->validateList();

        if (count($this->list) !== 0) {
            $this->primary = $this->list[0];
        }
    }

    public function isEmpty() : bool
    {
        return count($this->list) === 0;
    }

    public function getPrimary() : EmailAddress
    {
        if ($this->isEmpty()) {
            throw new RuntimeException("Can't get primary from empty group.");
        }

        return $this->primary;
    }

    /**
     * Get a list of all email addresses.
     *
     * @return array<EmailAddress>
     */
    public function getList() : array
    {
        return $this->list;
    }

    /**
     * Get a list of email addresses w/o a primary.
     *
     * @return array<EmailAddress>
     */
    public function getSecondaryList() : array
    {
        $list = [];

        foreach ($this->list as $item) {
            if ($item === $this->primary) {
                continue;
            }

            $list[] = $item;
        }

        return $list;
    }

    /**
     * Get a list of email addresses represented as strings.
     *
     * @return array<string>
     */
    public function getAddressList() : array
    {
        $list = [];

        foreach ($this->list as $item) {
            $list[] = $item->getAddress();
        }

        return $list;
    }

    /**
     * Whether an address is in the list.
     */
    public function hasAddress(string $address) : bool
    {
        return in_array($address, $this->getAddressList());
    }

    /**
     * Clone with another primary email address.
     */
    public function withPrimary(EmailAddress $emailAddress) : self
    {
        $list = $this->list;

        $index = $this->searchInList($emailAddress);

        if ($index !== null) {
            unset($list[$index]);

            $list = array_values($list);
        }

        $newList = array_merge([$emailAddress], $list);

        return self::fromList($newList);
    }

    /**
     * Create from an email address list. A first item will be set as primary.
     *
     * @param array<EmailAddress> $list
     */
    public static function fromList(array $list) : self
    {
        return new self($list);
    }

    private function searchInList(EmailAddress $emailAddress) : ?int
    {
        foreach ($this->list as $i => $item) {
            if ($item === $emailAddress) {
                return $i;
            }
        }

        return null;
    }

    private function validateList() : void
    {
        $addressList = [];

        foreach ($this->list as $item) {
            if (in_array($item->getAddress(), $addressList)) {
                throw new RuntimeException("Address list contains a duplicate.");
            }

            $addressList[] = $item->getAddress();
        }
    }
}
