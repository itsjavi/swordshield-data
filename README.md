# Pokémon Sword and Shield Data Dumps

> **IMPORTANT NOTICE**: This project won't be maintained anymore. Please use one of the following projects:
> - https://github.com/itsjavi/showdown-data
> - https://github.com/itsjavi/pokemon-assets
> - https://github.com/itsjavi/livingdex
> - https://github.com/itsjavi/livingdex-data

This project parses the RAW data dumps from the Pokémon Sword and Shield games
into machine-readable format (optimized JSON).

## Installation

For the ones only interested in the data:
```
git clone https://github.com/route1rodent/swordshield-data.git
```

For running the parser scripts: 
```
git clone https://github.com/route1rodent/swordshield-data.git
cd swordshield-data
composer install
```

Note that you will need at least PHP 7.3 and composer installed in order to be able to run the parsers.

## Usage

Once you clone this project you have available the `data` folder and the parsing scripts that generate the non "raw" part of it. All parsed data can be found already under the `data/json` directory, while the original source data dumps live under `data/raw`.

The parser script parses and exports all RAW data into JSON files, you only need to run it inside the cloned project directory:

```bash
./scripts/parse.php
```

## The optimized `json` format

Since reading a whole JSON file in memory might be expensive with large data sets, the JSON files of this project are
formatted in a way that each line represents a full JSON object in a list.

This way, you can read big files line by file, decoding each line from JSON to an object.

The only thing your JSON parser needs to do is to skip the first and last lines
(which are `[\n` and `{}]\n` respectively) and trim the trailing comma `,` of each line.


## Credits

Thanks to [@kwsch](https://github.com/kwsch) aka. [@Kaphotics](https://twitter.com/Kaphotics) and SciresM for the data dumps.

- https://pastebin.com/u/Kaphotics
- https://pastebin.com/u/SciresM
- https://github.com/kwsch

