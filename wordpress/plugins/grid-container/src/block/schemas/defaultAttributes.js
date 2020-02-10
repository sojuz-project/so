import { TECHNICAL, CLASS, STYLE, CONTENT, BLOCK, COMPONENT, SECTION, DATA } from './../index';
export const defaultAttributes = {
	[CLASS]: {
		type: 'object',
		default: {
			[BLOCK]: {
				'background-color': '',
				i: 0,
			},
			[COMPONENT]: [],
		},
	},
	[STYLE]: {
		type: 'object',
		default: {
			[BLOCK]: {
				gridTemplateColumns: 'repeat(1, 1fr)',
				gridGap: '0vw',
				padding: '0 0vw',
				i: 0,
			},
			[SECTION]: {
				gridTemplateRows: 'repeat(10, 1fr)',
				gridTemplateColumns: 'repeat(49, 1fr)',
			},
			[COMPONENT]: [],
		},
	},
	[TECHNICAL]: {
		type: 'object',
		default: {
			[BLOCK]: {
				i: 0,
				gridTemplateColumns: 1,
				padding: 0,
				gridGap: 0,
			},
			[SECTION]: {
				h: 10,
				gridTemplateColumns: 49,
			},
			[COMPONENT]: [],
		},
	},
	[DATA]: {
		type: 'object',
		default: {
			[BLOCK]: {
				i: 0,
				dataSources: '',
			},
			[COMPONENT]: [],
		},
	},
	[CONTENT]: {
		type: 'array',
		default: [[]],
	},
	/*
	 * ====
	 *	MUST BE FOR WORDPRESS GUTENBERG
	 * ====
	 */
	align: {
		type: 'string',
		default: 'full',
	},
	selectedElement: {
		type: 'object',
		default: {
			component: {},
			componentI: 0,
		},
	},
	i: {
		type: 'number',
		default: 0,
	},
};
