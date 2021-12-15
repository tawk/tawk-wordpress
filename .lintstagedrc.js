module.exports = {
	'*.js': 'eslint --cache --fix',
	'*.css': 'stylelint --cache --fix',
	'*.php': [
		'composer run lint:fix',
		'composer run lint'
	]
}
