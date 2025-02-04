<?php
/*
 * Github: https://github.com/kxle0801
 * Author: KxlePH
 */

declare(strict_types = 1);

namespace kxleph\kxlootbox\utils;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\Item;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\block\VanillaBlocks;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use pocketmine\item\StringToItemParser;

final class ItemUtils {

    const TAG_LOOTBOX = "tag.lootbox";
    const TAG_LOOTBOX_IDENTIFIER = "tag.lootbox.identifier";

    const CHEST_TYPE = "chest.type";
    const ENDERCHEST_TYPE = "enderchest.type";

    const TYPE_CUSTOM_LORE = "custom.type";
    const TYPE_ITEMS_LORE = "items.type";

    /**
     * @param string $itemType
     * @param string $lootboxName
     * @param int $count
     * @param array[] $lores
     * @param bool $isGlint
     * @return Item|null
     */
    public static function giveLootboxItem(string $itemType, string $lootboxName, string $identifier, int $count, array $lores = [], bool $isGlint = true): ?Item {
        $lootbox = $itemType === self::CHEST_TYPE ? VanillaBlocks::CHEST()->asItem() : VanillaBlocks::ENDER_CHEST()->asItem();
        if ($isGlint)  $lootbox->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(-1)));
        
        $lootbox->setCustomName($lootboxName)->setCount($count)->setLore($lores);
        $lootbox->setNamedTag($lootbox->getNamedTag()->setString(self::TAG_LOOTBOX, $lootboxName)->setString(self::TAG_LOOTBOX_IDENTIFIER, $identifier));
        return in_array($itemType, [self::CHEST_TYPE, self::ENDERCHEST_TYPE]) ? $lootbox : null;
    }    

    /**
     * @param array $contents
     * @return string
     */
    public static function encodeContent(array $contents): string {
        $lootboxItems = [];
        foreach ($contents as $content) $lootboxItems[] = self::encodeItem($content);
        return json_encode($lootboxItems);
    }

    /**
     * @param string $lootboxData
     * @return array|null
     */
    public static function decodeContent(string $lootboxData): ?array {
        $lootboxItems = json_decode($lootboxData);
        if (!is_null($lootboxItems)) return $lootboxItems;
        return null;
    }

    /**
     * @param string $item
     * @return Item
     */
    public static function getItem(string $item): Item {
        return StringToItemParser::getInstance()->parse($item);
    }

    /**
     * @param Item $item
     * @return string
     */
    public static function encodeItem(Item $item): string {
        $serializer = new LittleEndianNbtSerializer();
        return base64_encode(gzcompress($serializer->write(new TreeRoot($item->nbtSerialize()))));
    }
    
    /**
     * @param string $data
     * @return Item
     */
    public static function decodeItem(string $data): Item {
        $serializer = new LittleEndianNbtSerializer();
        return Item::nbtDeserialize($serializer->read(gzuncompress(base64_decode($data)))->mustGetCompoundTag());
    }
}
