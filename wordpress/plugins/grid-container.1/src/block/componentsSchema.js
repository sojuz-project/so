export const componentsSchema = {
	Paragraph: {
		blockName: 'coreparagraph',
		name: 'Paragraph',
		innerHTML: '',
		mapQL: [],
		attrs: {
			innerHTML:
				'<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua...</p>',
		},
		defaultValues: {
			align: 'left',
			fontSize: 'medium',
			weight: 400,
			reasignTo: '',
			textColor: 'black',
			backgroundColor: '',
			backgroundOpacity: 0.5,
			verticalAlign: 'top',
			padding: 0,
		},
	},
	Image: {
		blockName: 'customimage',
		name: 'Image',
		innerHTML: '',
		mapQL: [],
		attrs: {},
		defaultValues: {
			layout: {},
			backgroundColor: '',
			backgroundOpacity: 0.5,
		},
	},
	Heading: {
		blockName: 'coreheading',
		name: 'Heading',
		innerHTML: '',
		mapQL: [],
		attrs: {
			innerHTML: 'Probably best example title',
		},
		defaultValues: {
			align: 'left',
			textColor: 'black',
			weight: 400,
			backgroundColor: '',
			backgroundOpacity: 0.5,
			padding: 0,
			tagName: 'h2',
			verticalAlign: 'top',
		},
	},
	ComponentWrapper: {
		blockName: 'componentwrapper',
		name: 'Empty space',
		innerHTML: '',
		mapQL: [],
		attrs: {},
		defaultValues: {
			backgroundOpacity: 1,
			backgroundColor: '',
		},
	},
	Action: {
		blockName: 'customaction',
		name: 'Action',
		innerHTML: '',
		mapQL: [],
		attrs: {},
	},
};
