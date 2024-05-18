<?php
/*
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 * For more information about the GNU Lesser General Public License and
 * how it applies to this software, please see the LICENSE file included
 * with this distribution or visit the GNU website at <https://www.gnu.org/>.
 * 
 * Github: https://github.com/kxle0801
 * Author: KxlePH
 */

declare(strict_types = 1);

namespace kxle\command\subcommands;

use kxle\KXLootbox;

use kxle\utils\KXSourceUtils;
use kxle\utils\PermissionIds;

use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\args\RawStringArgument;

class KXLootboxDelete extends BaseSubCommand {

	/**
	 * @return void
	 */
	public function prepare(): void {
		$this->setPermission(PermissionIds::KXLOOTBOX_COMMAND_DELETE);
		$this->registerArgument(0, new RawStringArgument("identifier", true));
	}

	/**
	 * @param CommandSender $sender
	 * @param string $aliasUsed
	 * @param array $args
	 * @return void
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		$plugin = KXLootbox::getInstance();
		$config = $plugin->getConfig();
		$message = KXSourceUtils::getMessages();
	
		if (!isset($args["identifier"])) {
			$sender->sendMessage($config->get("prefix") . " " . str_replace("{base-cmd}", $config->get("base-cmd"), $config->get("sub-cmd-delete-usage")));
			return;
		}

		$kxData = KXSourceUtils::getKXBoxData()->get($args["identifier"]);
		if (!is_array($kxData) || !isset($kxData['identifier']) || $kxData['identifier'] !== $args["identifier"]) {
			$sender->sendMessage($config->get("prefix") . " " . str_replace("{identifier}", $args["identifier"], $message->get("sub-cmd-NoId")));
			return;
		}

		KXSourceUtils::getKXBoxData()->remove($args["identifier"]);
		$sender->sendMessage($config->get("prefix") . " " . str_replace("{identifier}", $args["identifier"], $message->get("sub-cmd-Removed")));
	}
}