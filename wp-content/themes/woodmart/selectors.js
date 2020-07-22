const readline = require('readline');
const fs = require('fs');

function selectors(cb) {

	var lineReader = readline.createInterface({
		input: fs.createReadStream('./style.css')
	});

	var groups = [
		{ id: 'primary-color' },
		{ id: 'primary-background' },
		{ id: 'primary-border' },
		{ id: 'primary-stroke' },
		{ id: 'alternative-color' },
		{ id: 'alternative-background' },
		{ id: 'alternative-border' },
		{ id: 'text-font' },
		{ id: 'primary-font' },
		{ id: 'secondary-font' },
		{ id: 'titles-font' },
		{ id: 'widget-titles-font' },
		{ id: 'navigation-font' },
		{ id: 'btns-default' },
		{ id: 'btns-accent' },
		{ id: 'btns-shop' },
		{ id: 'link-color' }
	];

	var found = false;
	var id = '';
	lineReader.on('line', function (line) {

		if (found) {
			groups.forEach(function (group) {
				if (group.id == id) {
					group.selector = line.substring(0, line.length - 2);
				}
			});
			found = false;
		}

		groups.forEach(function (group) {
			if (line == '/* ' + group.id + ' */') {
				found = true;
				id = group.id;
				console.log('Line from file:', line);
			}
		});

	});

	lineReader.on('close', function (line) {
		console.log('done', groups);
		updateSelectorsConfig(groups);
	});

	function updateSelectorsConfig(groups) {
        var fileContent = [
			"<?php if ( ! defined('WOODMART_THEME_DIR')) exit('No direct script access allowed');\n",
			'/**\n',
			' * Prepare CSS selectors for theme settions (colors, borders, typography etc.)\n',
			' */\n',
			'\n',
			'return apply_filters(\n',
			"\t'woodmart_get_selectors',\n",
			'\tarray(\n',
			'\t\t\'primary-color\' => array(\n',
			'\t\t\t\'color\' => woodmart_text2line( \'%primary-color%\' ),\n',
			'\t\t\t\'background-color\' => woodmart_text2line( \'%primary-background%\' ),\n',
			'\t\t\t\'border-color\' => woodmart_text2line( \'%primary-border%\' ),\n',
			'\t\t\t\'stroke\' => woodmart_text2line( \'%primary-stroke%\' ),\n',
			'\t\t),\n',
			'\t\t\'secondary-color\' => array(\n',
			'\t\t\t\'color\' => woodmart_text2line( \'%alternative-color%\' ),\n',
			'\t\t\t\'background-color\' => woodmart_text2line( \'%alternative-background%\' ),\n',
			'\t\t\t\'border-color\' => woodmart_text2line( \'%alternative-border%\' ),\n',
			'\t\t),\n',
			'\t\t\'text-font\' => array( \'%text-font%\' ),\n',
			'\t\t\'primary-font\' => array( \'%primary-font%\' ),\n',
			'\t\t\'secondary-font\' => array( \'%secondary-font%\' ),\n',
			'\t\t\'titles-font\' => array( \'%titles-font%\' ),\n',
			'\t\t\'widget-titles-font\' => array( \'%widget-titles-font%\' ),\n',
			'\t\t\'navigation-font\' => array( \'%navigation-font%\' ),\n',
			'\t\t\'btns-default\' => array( \'%btns-default%\' ),\n',
			'\t\t\'btns-accent\' => array( \'%btns-accent%\' ),\n',
			'\t\t\'btns-shop\' => array( \'%btns-shop%\' ),\n',
			'\t\t\'link-color\' => array( \'%link-color%\' ),\n',
			'));',
		].join('');

		var btnsFile = [
			'$default-button-selectors: "%btns-default%";\n',
			'$shop-button-selectors: "%btns-shop%";\n',
			'$accent-button-selectors: "%btns-accent%";\n',
		].join('');
	
		groups.forEach(function (group) {
			fileContent = fileContent.replace("%" + group.id + "%", group.selector );
			btnsFile = btnsFile.replace("%" + group.id + "%", group.selector );
		});

		fs.writeFile('./inc/configs/selectors.php', fileContent, function (err) {
			if (err) {
				return console.log(err);
			}
			console.log("The file was saved!");
		});

		fs.writeFile('./sass/_configs-selectors.scss', btnsFile, function (err) {
			if (err) {
				return console.log(err);
			}
			console.log("The file was saved!");
		});

	}
}

selectors();