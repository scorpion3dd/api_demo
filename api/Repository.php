<?php
/*
 * This file is part of the Simple REST Full API project.
 *
 * (c) Denis Puzik <scorpion3dd@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace api;

/**
 * Interface Repository
 * @package patterns
 */
interface Repository
{
    /**
     * @return array
     */
    public function getAll(): array;

    /**
     * @param int $id
     * @return \stdClass|null
     */
    public function getById($id = 0);

    /**
     * @return bool
     */
    public function update(): bool;

    /**
     * @param $userId
     * @return bool
     */
    public function deleteById($userId): bool;

    /**
     * @return int
     */
    public function saveNew(): int;
}
