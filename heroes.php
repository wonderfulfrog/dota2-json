<?php

include('vdfparser.php');

$language_data_file_suffix = 'english';
$lang = isset($_GET['lang']) ? htmlspecialchars($_GET['lang']) : 'english';
if($lang) $language_data_file_suffix = $lang;
$language_data_file = 'dota_' . $language_data_file_suffix . '.txt';

if(!file_exists('data/' . $language_data_file)) die('Error: could not find language data file ' . $language_data_file);

$hero_data = VDFParse('data/npc_heroes.txt');
$spells_data = VDFParse('data/npc_abilities.txt');
$activelist_data = VDFParse('data/activelist.txt');
$language_data = VDFParse('data/' . $language_data_file);

$spells = $spells_data['DOTAAbilities'];
$activelist = $activelist_data['whitelist'];
$lang = $language_data['lang']['Tokens'];

$hero_keys_to_ignore = array(
	'Version',
	'npc_dota_hero_base'
);

$spell_keys_to_ignore = array(
	'attribute_bonus'
);

$prop_keys_to_ignore = array(
	'var_type'
);

$hero_primary_attributes = array(
	'DOTA_ATTRIBUTE_STRENGTH' => 'str',
	'DOTA_ATTRIBUTE_AGILITY' => 'agi',
	'DOTA_ATTRIBUTE_INTELLECT' => 'int'
);


foreach($hero_data['DOTAHeroes'] as $key => $hero) {
	if(in_array($key, $hero_keys_to_ignore)) continue;

	$hero_name_basic = str_replace('npc_dota_hero_', '', $key);

	// Hero Data
	$heroes[$hero_name_basic] = array(
		'id' => $hero['HeroID'],
		'name' => $lang['npc_dota_hero_' . $hero_name_basic],
		'team' => $hero['Team'] == 'Good' ? 'radiant' : 'dire',
		'type' => $hero["AttackCapabilities"] == "DOTA_UNIT_CAP_RANGED_ATTACK" ? "ranged" : "melee",
		'primary_attr' => $hero_primary_attributes[$hero['AttributePrimary']],
		'base_str' => $hero['AttributeBaseStrength'],
		'str_per_level' => $hero['AttributeStrengthGain'],
		'base_agi' => $hero['AttributeBaseAgility'],
		'agi_per_level' => $hero['AttributeAgilityGain'],
		'base_int' => $hero['AttributeBaseIntelligence'],
		'int_per_level' => $hero['AttributeIntelligenceGain'],
		'base_damage_min' => $hero['AttackDamageMin'],
		'base_damage_max' => $hero['AttackDamageMax'],
		'base_movement_speed' => $hero['MovementSpeed'],
		'turn_rate' => $hero['MovementTurnRate'],
		'base_armor' => $hero['ArmorPhysical'],
		'active' => isset($activelist[$key]) && $activelist[$key] == 1 ? 1 : 0,
		'bio' => isset($lang['npc_dota_hero_' . $hero_name_basic . '_bio']) ? $lang['npc_dota_hero_' . $hero_name_basic . '_bio'] : '',
		'spells' => array()
	);

	// Spells
	$i = 1;
	while(isset($hero['Ability' . $i])) {
		$spell_name_basic = $hero['Ability' . $i];
		if(in_array($spell_name_basic, $spell_keys_to_ignore)) { $i++; continue; }

		// Parse behavior types
		$behaviors_parsed = "";
		if(isset($spells[$spell_name_basic]['AbilityBehavior'])) {
			$behaviors = explode(" | ", $spells[$spell_name_basic]['AbilityBehavior']);

			foreach($behaviors as $behavior) {
				if($behavior == "DOTA_ABILITY_BEHAVIOR_PASSIVE")
					$behaviors_parsed .= "Passive";

				if($behavior == "DOTA_ABILITY_BEHAVIOR_POINT")
					$behaviors_parsed .= (strlen($behaviors_parsed) > 0) ? ", Target Point" : "Target Point";

				if($behavior == "DOTA_ABILITY_BEHAVIOR_AUTOCAST")
					$behaviors_parsed .= (strlen($behaviors_parsed) > 0) ? ", Auto-Cast" : "Auto-Cast";

				if($behavior == "DOTA_ABILITY_BEHAVIOR_AURA")
					$behaviors_parsed .= (strlen($behaviors_parsed) > 0) ? ", Aura" : "Aura";

				if($behavior == "DOTA_ABILITY_BEHAVIOR_UNIT_TARGET")
					$behaviors_parsed .= (strlen($behaviors_parsed) > 0) ? ", Target Unit " : "Target Unit";

				if($behavior == "DOTA_ABILITY_BEHAVIOR_NO_TARGET")
					$behaviors_parsed .= (strlen($behaviors_parsed) > 0) ? ", No Target" : "No Target";

				if($behavior == "DOTA_ABILITY_BEHAVIOR_CHANNELLED")
					$behaviors_parsed .= (strlen($behaviors_parsed) > 0) ? ", Channeled" : "Channeled";
			}
		}

		// Parse target types
		$affects = "";

		if(isset($spells[$spell_name_basic]['AbilityUnitTargetTeam']) && isset($spells[$spell_name_basic]['AbilityUnitTargetType'])) {
			$affects_team = $spells[$spell_name_basic]['AbilityUnitTargetTeam'];
			$affects_type = explode(" | ", $spells[$spell_name_basic]['AbilityUnitTargetType']);

			if($affects_team == "DOTA_UNIT_TARGET_TEAM_FRIENDLY")
			{
				if(sizeof($affects_type) > 0) {
					$affects .= "Allied";
					if($affects_type[0] == "DOTA_UNIT_TARGET_HERO")
						$affects .= " Heroes";
					else
						$affects .= " Units";
				}
				else
					$affects .= "Allies";
			}
			else if($affects_team == "DOTA_UNIT_TARGET_TEAM_ENEMY")
			{
				if(sizeof($affects_type) > 0) {
					$affects .= "Enemy";
					if($affects_type[0] == "DOTA_UNIT_TARGET_HERO")
						$affects .= " Heroes";
					else
						$affects .= " Units";

				}
				else
					$affects .= "Enemies";
			}
			else
			{
				if($affects_type[0] == "DOTA_UNIT_TARGET_HERO")
						$affects .= "Heroes";
			}
		}

		// Damage type
		$damage_type = '';
		if(isset($spells[$spell_name_basic]['AbilityUnitDamageType'])) {
			$damage_type = $spells[$spell_name_basic]['AbilityUnitDamageType'];
			preg_match("/DAMAGE_TYPE_(.*?)$/", $damage_type, $matches);
			$damage_type = sizeof($matches) > 0 ? ucwords(strtolower(str_replace("_", " ", $matches[1]))) : '';
		}
		
		$heroes[$hero_name_basic]['spells'][$spell_name_basic] = array(
			'id' => isset($spells[$spell_name_basic]['ID']) ? $spells[$spell_name_basic]['ID'] : '',
			'name' => isset($lang['DOTA_Tooltip_ability_' . $spell_name_basic]) ? $lang['DOTA_Tooltip_ability_' . $spell_name_basic] : '',
			'mana_cost' => isset($spells[$spell_name_basic]['AbilityManaCost']) ? $spells[$spell_name_basic]['AbilityManaCost'] : '',
			'cooldown' => isset($spells[$spell_name_basic]['AbilityCooldown']) ? $spells[$spell_name_basic]['AbilityCooldown'] : '',
			'damage' => isset($spells[$spell_name_basic]['AbilityDamage']) ? $spells[$spell_name_basic]['AbilityDamage'] : '',
			'targets' => $behaviors_parsed,
			'affects' => $affects,
			'damage_type' => $damage_type,
			'pierces_spell_immunity' => isset($spells[$spell_name_basic]['SpellImmunityType']) && $spells[$spell_name_basic]['SpellImmunityType'] == 'SPELL_IMMUNITY_ENEMIES_YES' ? 1 : 0,
			'description' => isset($lang['DOTA_Tooltip_ability_' . $spell_name_basic . '_Description']) ? $lang['DOTA_Tooltip_ability_' . $spell_name_basic . '_Description'] : '',
			'lore' => isset($lang['DOTA_Tooltip_ability_' . $spell_name_basic . '_Lore']) ? $lang['DOTA_Tooltip_ability_' . $spell_name_basic . '_Lore'] : '',
			'properties' => array()
		);

		// Spell properties
		if(isset($spells[$spell_name_basic]['AbilitySpecial'])) {
			foreach($spells[$spell_name_basic]['AbilitySpecial'] as $property_list) {
				foreach($property_list as $prop_key => $prop) {
					if(in_array($prop_key, $prop_keys_to_ignore)) continue;

					$heroes[$hero_name_basic]['spells'][$spell_name_basic]['properties'][$prop_key] = array(
						'name' => isset($lang['DOTA_Tooltip_ability_' . $spell_name_basic . '_' . $prop_key]) ? $lang['DOTA_Tooltip_ability_' . $spell_name_basic . '_' . $prop_key] : '',
						'value' => $prop
					);
				}
			}
		}

		// Text cleanup
		// Replaces values like %property_key% with the stored value, and other things like double percents (%%)
		$spell_description = $heroes[$hero_name_basic]['spells'][$spell_name_basic]['description'];
		foreach($heroes[$hero_name_basic]['spells'][$spell_name_basic]['properties'] as $prop_key => $prop) {
			$spell_description = str_replace('%' . $prop_key . '%', $prop['value'], $spell_description);
			if(isset($prop['name'][0]) && $prop['name'][0] == '%') {
				$heroes[$hero_name_basic]['spells'][$spell_name_basic]['properties'][$prop_key]['name'] = substr($prop['name'], 1);
				$values = explode(" ", $prop['value']);
				foreach($values as $key => $value) {
					$values[$key] .= '%';
				}
				$heroes[$hero_name_basic]['spells'][$spell_name_basic]['properties'][$prop_key]['value'] = implode(" ", $values);
			}
		}

		$spell_description = str_replace('%%', '%', $spell_description);
		$heroes[$hero_name_basic]['spells'][$spell_name_basic]['description'] = $spell_description;

		$i++;

	}
}

header('Content-Type: application/json');
echo json_encode($heroes);

?>
