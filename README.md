dota2-json
==========

dota2-json converts the [VDF](https://developer.valvesoftware.com/wiki/KeyValues) (Valve Data Format, also referred to as KeyValues) game files from Dota 2 into easily usable JSON.

Requirements
------------
[keyvalues-php](https://github.com/devinwl/keyvalues-php)

Setup
-----
1. Add the necessary data files to the `data` folder:
 * `npc_heroes.txt`
 * `npc_abilities.txt`
 * `activelist.txt`
 * `dota_english.txt`

 These files are not included and must be retrieved using a program capable of reading `gcf` files, such as [GCFScape](http://nemesis.thewavelength.net/index.php?p=25).
2. Make sure `vdfparser.php` is located in the same directory as `heroes.php`.
3. Run `heroes.php` to generate the JSON file for all hero data.

Schema
------

#### Hero

Key                 | Value
--------------------|-------------------------------------------------------------------------------
id                  | The internal ID of the hero.
name                | The name of the hero as it appears in the language file (defaults to English).
team                | The side the hero is associated with.
type                | The type of the hero, either `melee` or `ranged`.
primary_attr        | The Primary Attribute of the hero.  Can be either `str`, `agi`, or `int`
base_str            | The starting Strength of the hero.
str_per_level       | The amount of Strength a hero gains per level.
base_agi            |The starting Agility of the hero.
agi_per_level       |The amount of Agility a hero gains per level.
base_int            |The starting Intelligence of the hero.
int_per_level       | The amount of Intelligence a hero gains per level.
base_damage_min     | The minimum starting damage of the hero.
base_damage_max     | The maximum starting damage of the hero.
base_movement_speed | The base movement speed of the hero.
turn_rate           | The hero's turn rate (how quickly they can change directions).
base_armor          | The starting Armor of the hero.
active              | (boolean) Whether or not the hero is available for public play.
bio                 | The description/lore for the hero (defaults to English).
spells              | (list of objects) A list of all of the heroes' abilities.

### Ability
Key                    | Value
-----------------------|-------------------------------------------------------------------------------------------------
id                     | The internal ID of the ability.
name                   | The name of the ability as it appears in the language file (defaults to English).
mana_cost              | The mana cost(s) of the ability.  A space-seperated string corresponding to each level of the ability.
cooldown               | The cooldown(s) of the ability.  A space-seperated string corresponding to each level of the ability.
damage                 | The damage value(s) of the ability.  A space-seperated string corresponding to each level of the ability.  This value is not always populated, as the damage values can be contained inside the property list of the ability.
targets                | The target(s) of the ability.  A comma-seperated string.
affects                | The unit type(s) affected by the ability.  A comma-seperated string.
damage_type            | The damage type of the ability.  Can be either `Physical`, `Magical`, or `Pure`.
pierces_spell_immunity | (boolean) Whether or not this ability pierces spell immunity.
description            | A text description about the ability (defaults to English).
lore                   | Flavor text that appears near the bottom of the ability in-game (defaults to English).
properties             | (list of objects) Various properites about the ability.

### Ability Property
Key      | Value
---------|----------------------------------------------------------------------------------
name     | The name of the property as it appears in the language file (defaults to English)
value    | The space-separated values that correspond to the property.

Samples
--------
Examples from the JSON file are included here.  Some parts have been truncated for brevity.

#### Hero
```json
"techies": {
  "id": "105",
  "name": "Techies",
  "team": "radiant",
  "type": "ranged",
  "primary_attr": "int",
  "base_str": "17",
  "str_per_level": "2.0",
  "base_agi": "14",
  "agi_per_level": "1.3",
  "base_int": "22",
  "int_per_level": "2.9",
  "base_damage_min": "7",
  "base_damage_max": "9",
  "base_movement_speed": "270",
  "turn_rate": "0.5",
  "base_armor": "5",
  "active": 1,
  "bio": "In the storied saga of Dredger's Bight, ...",
  "spells": {
  }
}
```

### Ability
```json
"techies_remote_mines": {
  "id": "5602",
  "name": "Remote Mines",
  "mana_cost": "200 240 300",
  "cooldown": "10.0 10.0 10.0",
  "damage": "",
  "targets": "Target Point",
  "affects": "",
  "damage_type": "Magical",
  "pierces_spell_immunity": 0,
  "description": "Plant an invisible explosive that will only detonate when triggered. ...",
  "lore": "The downfall of Dredger's Bight!",
  "properties": {
    "damage": {
      "name": "DAMAGE:",
      "value": "300 450 600"
    },
    "radius": {
      "name": "EXPLOSION RADIUS:",
      "value": "425"
    },
    "duration": {
      "name": "MINE DURATION:",
      "value": "600.0"
    },
    "activation_time": {
      "name": "FADE TIME:",
      "value": "2.0"
    },
    "cast_range_tooltip": {
      "name": "CAST RANGE:",
      "value": "500"
    },
    "damage_scepter": {
      "name": "SCEPTER DAMAGE:",
      "value": "450 600 750"
    },
    "cast_range_scepter": {
      "name": "SCEPTER CAST RANGE:",
      "value": "700"
    }
  }
}
```

License
-------
```
The MIT License (MIT)

Copyright (c) 2015 Devin Lumley

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
