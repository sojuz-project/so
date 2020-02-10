let i = 1;

export const componentsSchema = {
	/* PARAGRAPH */
	coreparagraph: {
		defaultTechnical: {
			h: 4,
			i: `n${++i}`,
			mapQL: undefined,
			reasignTo: undefined,
			x: 0,
			y: 4,
			w: 30,
			padding: 0,
		},
		defaultContent: {
			blockName: 'coreparagraph',
			innerHTML: '<p>Default</p>',
		},
		defaultClass: {},
		defaultStyle: {},
	},
	/* HEADING */
	coreheading: {
		defaultTechnical: {
			h: 4,
			i: `n${++i}`,
			mapQL: undefined,
			reasignTo: undefined,
			tagName: 'h2',
			x: 0,
			y: 1,
			w: 30,
			x2: 30,
			y2: 5,
			z: 1,
			padding: 0,
		},
		defaultContent: {
			blockName: 'coreheading',
			innerHTML: 'Default',
		},
		defaultClass: {},
		defaultStyle: {},
	},
	/* IMAGE */
	customimage: {
		defaultTechnical: {
			h: 6,
			i: `n${++i}`,
			mapQL: undefined,
			reasignTo: undefined,
			x: 0,
			x2: 20,
			y: 0,
			y2: 6,
			w: 20,
			z: 1,
		},
		defaultContent: {
			blockName: 'customimage',
			alt: undefined,
			url: undefined,
		},
		defaultClass: {},
		defaultStyle: {},
	},
	/* WRAPPER */
	customwrapper: {
		defaultTechnical: {
			h: 4,
			i: `n${++i}`,
			mapQL: undefined,
			reasignTo: undefined,
			x: 0,
			x2: 4,
			y: 0,
			y2: 4,
			w: 4,
			padding: 0,
		},
		defaultContent: {
			blockName: 'customwrapper',
		},
		defaultClass: {},
		defaultStyle: {},
	},
};
