<?php

namespace route1rodent\SwordShieldData;

class PokemonParser extends Parser
{
    public function __construct()
    {
        parent::__construct('sword_shield_pokemon_stats.txt', 'pokemon.json');
    }

    protected function parseBlock(?array $block): ?array
    {
        $pokemon = [
            'id' => null,
            'name' => null,
            'stage' => 0,
            'galar_dex' => null,
            'base_stats' => [],
            'ev_yield' => [],
            'abilities' => [],
            'types' => [],
            'items' => [],
            'exp_group' => null,
            'egg_groups' => [],
            'hatch_cycles' => 0,
            'height' => null,
            'weight' => null,
            'color' => null,
            'level_up_moves' => [],
            'egg_moves' => [],
            'tms' => [],
            'trs' => [],
            'evolutions' => [],
            'description' => null,
        ];

        if (empty($block)) {
            return null;
        }

        $header = array_shift($block);

        // Species
        if (!preg_match_all('/^(?<id>[0-9]+) - (?<name>.*) \(Stage: (?<stage>[0-9])\)/i', $header, $data)) {
            throw new ParseException("Cannot parse pokemon header: '{$header}'");
        }
        $pokemon['id'] = intval($data['id'][0]);
        $pokemon['name'] = trim($data['name'][0]);
        $pokemon['stage'] = (int)$data['stage'][0];

        $listName = null;
        $listRegex = null;

        $offset = 0;
        foreach ($block as $i => $line) {
            $offset = $i;
            if ($listRegex && preg_match_all($listRegex, $line, $data) > 0) {
                switch ($listName) {
                    case 'level_up_moves':
                        {
                            $level = ltrim($data['level'][0], '0');
                            $pokemon['level_up_moves'][] = [
                                is_numeric($level) ? (int)$level : ($level ? $level : 0),
                                $data['move'][0]
                            ];
                        }
                        break;
                    case 'egg_moves':
                        {
                            $pokemon['egg_moves'][] = $data['move'][0];
                        }
                        break;
                    case 'tms':
                        {
                            $pokemon['tms'][] = intval($data['id'][0]);
                        }
                        break;
                    case 'trs':
                        {
                            $pokemon['trs'][] = intval($data['id'][0]);
                        }
                        break;
                }
                continue;
            } else {
                $listName = null;
                $listRegex = null;
            }

            switch (true) {
                case preg_match_all('/^Galar Dex\: \#?(?<dex>[0-9a-z]*)/i', $line, $data) > 0:
                    {
                        $pokemon['galar_dex'] = mb_strtolower((string)$data['dex'][0]);
                    }
                    break;
                case preg_match_all('/^Base Stats\: (?<hp>[0-9]{1,3})\.(?<atk>[0-9]{1,3})\.(?<def>[0-9]{1,3})\.(?<spa>[0-9]{1,3})\.(?<spd>[0-9]{1,3})\.(?<spe>[0-9]{1,3})/i', $line, $data) > 0:
                    {
                        $pokemon['base_stats'] = [
                            (int)$data['hp'][0],
                            (int)$data['atk'][0],
                            (int)$data['def'][0],
                            (int)$data['spa'][0],
                            (int)$data['spd'][0],
                            (int)$data['spe'][0],
                        ];
                    }
                    break;
                case preg_match_all('/^EV Yield\: (?<hp>[0-9])\.(?<atk>[0-9])\.(?<def>[0-9])\.(?<spa>[0-9])\.(?<spd>[0-9])\.(?<spe>[0-9])/i', $line, $data) > 0:
                    {
                        $pokemon['ev_yield'] = [
                            (int)$data['hp'][0],
                            (int)$data['atk'][0],
                            (int)$data['def'][0],
                            (int)$data['spa'][0],
                            (int)$data['spd'][0],
                            (int)$data['spe'][0],
                        ];
                    }
                    break;
                case preg_match_all('/^Abilities\: (?<ab1>.+) \(1\) \| (?<ab2>.+) \(2\) \| (?<ab3>.+) \(H\)/i', $line, $data) > 0:
                    {
                        $pokemon['abilities'][] = $data['ab1'][0];
                        $pokemon['abilities'][] = $data['ab2'][0];
                        $pokemon['abilities'][] = $data['ab3'][0];
                    }
                    break;
                case preg_match_all('/^Type\: (?<types>.+)/i', $line, $data) > 0:
                    {
                        $types = array_map(function ($type) {
                            return trim($type);
                        }, explode('/', $data['types'][0]));
                        $pokemon['types'][] = $types[0];
                        if (isset($types[1])) {
                            $pokemon['types'][] = $types[1];
                        }
                    }
                    break;
                case preg_match_all('/^Item [0-9] \((?<ratio>[0-9]+)%\): (?<item>.+)/i', $line, $data) > 0:
                    {
                        $pokemon['items'][] = [$data['item'][0], floatval($data['ratio'][0])];
                    }
                    break;
                case preg_match_all('/^EXP Group: (?<value>.+)/i', $line, $data) > 0:
                    {
                        $pokemon['exp_group'] = $data['value'][0];
                    }
                    break;
                case preg_match_all('/^Egg Group\: (?<value>.+)/i', $line, $data) > 0:
                    {
                        $values = array_map(function ($value) {
                            return trim($value);
                        }, explode('/', $data['value'][0]));
                        $pokemon['egg_groups'][] = $values[0];
                        if (isset($values[1])) {
                            $pokemon['egg_groups'][] = $values[1];
                        }
                    }
                    break;
                case preg_match_all('/^Hatch Cycles: (?<value>.+)/i', $line, $data) > 0:
                    {
                        $pokemon['hatch_cycles'] = (int)$data['value'][0];
                    }
                    break;
                case preg_match_all('/^Height\: (?<height>.+)m, Weight\: (?<weight>.+)kg, Color\: (?<color>.+)/i', $line, $data) > 0:
                    {
                        $pokemon['height'] = floatval($data['height'][0]);
                        $pokemon['weight'] = floatval($data['weight'][0]);
                        $pokemon['color'] = $data['color'][0];
                    }
		    break;
		case preg_match_all('/^Catch Rate\: (?<value>.+)/i', $line, $data) > 0:
		    {
			$pokemon['catch_rate'] = (int)$data['value'][0];
		    }
		    break;
                case preg_match('/^Level Up Moves\:/i', $line) > 0:
                    {
                        $listName = 'level_up_moves';
                        $listRegex = '/^- \[(?<level>[0-9]*)\] (?<move>.*)/i';
                    }
                    break;
                case preg_match('/^Egg Moves\:/i', $line) > 0:
                    {
                        $listName = 'egg_moves';
                        $listRegex = '/^- (?<move>.*)/i';
                    }
                    break;
                case preg_match('/^TMs\:/i', $line) > 0:
                    {
                        $listName = 'tms';
                        $listRegex = '/^- \[TM(?<id>[0-9]*)\] (?<move>.*)/i';
                    }
                    break;
                case preg_match('/^TRs\:/i', $line) > 0:
                    {
                        $listName = 'trs';
                        $listRegex = '/^- \[TR(?<id>[0-9]*)\] (?<move>.*)/i';
                    }
                    break;
                case preg_match_all('/^Evolves into (?<pkm>.*) \@ (?<level>[0-9]{1,3}) \((?<method>.*)\) \[(?<ref>.*)\]/i', $line, $data) > 0:
                    {
                        $pokemon['evolutions'][] = [
                            'species' => $data['pkm'][0],
                            'method' => $data['method'][0],
                            'method_value' => $data['method'][0] === 'LevelUp' ? intval($data['level'][0]) : $data['ref'][0],
                        ];
                    }
                    break;
            }
        }

        $description = trim(implode(PHP_EOL, array_slice($block, $offset)));

        if (!preg_match('/^- /', $description)) {
            $pokemon['description'] = $description;
        }

        return $pokemon;
    }
}
