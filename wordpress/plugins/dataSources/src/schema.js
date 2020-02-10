export const compSchema = {
	post_title: {
		blockName: 'coreheading',
		name: 'Heading',
		targetAttr: 'innerHTML',
	},
	post_name: {
		blockName: 'coreparagraph',
		name: 'Name',
		targetAttr: 'innerHTML',
	},
	post_excerpt: {
		blockName: 'coreparagraph',
		name: 'Excerpt',
		targetAttr: 'innerHTML',
	},
	post_content: {
		blockName: 'coreparagraph',
		name: 'Paragraph',
		targetAttr: 'innerHTML',
	},
	thumbnail: {
		blockName: 'customimage',
		name: 'CustomImage',
		targetAttr: false, // add to attrs directly
	},
	post_meta: {
		blockName: 'componentwrapper',
		name: 'Meta data',
		targetAttr: 'meta',
		reasignTo: 'SingleMeta',
	},
	id: {
		blockName: 'componentwrapper',
		name: 'Component wrapper',
		targetAttr: 'id',
	},
	action: {
		blockName: 'customaction',
		name: 'action',
		targetAttr: 'action',
	},
};
