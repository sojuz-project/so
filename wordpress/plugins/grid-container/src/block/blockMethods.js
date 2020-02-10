import { componentsSchema } from './schemas/componentsSchema';
const { set, get: _get } = lodash;

export const get = (path, defaultValue) => _get(props.attributes, path, defaultValue);

export const setAttrs = (setAttributes) => (path, cb = (v) => v) => (val) => {
	const newAttrs = set(props.attributes, path, val);
	/* all {i} fix gutenberg dafault values problem */
	const attrs = {
		i: ++i,
		...newAttrs,
		class: {
			...newAttrs.class,
			block: {
				...newAttrs.class.block,
				i: ++i,
			},
		},
		style: {
			...newAttrs.style,
			block: {
				...newAttrs.style.block,
				i: ++i,
			},
		},
		technical: {
			...newAttrs.technical,
			block: {
				...newAttrs.technical.block,
				i: ++i,
			},
		},
		data: {
			...newAttrs.data,
			block: {
				...newAttrs.data.block,
				i: ++i,
			},
		},
	};
	setAttributes(cb(attrs));
};

// React.useEffect(() => {
// 	setAttrs(`${TECHNICAL}.${BLOCK}.clientId`)(props.clientId)
// 	// update(TECHNICAL, BLOCK, curr => ({ ...curr, clientId: props.clientId }))
// }, [])

// const addSection = () => {
// 	setAttrs(`${CONTENT}`)([
// 		...get(CONTENT),
// 		[{
// 			innerHTML: '',
// 		}],
// 	])
// 	setGridMode(prev => true);
// };

export const newComponent = (blockName) => {
	const schema = componentsSchema[blockName];
	const lastComponent = technicalComponent[technicalComponent.length - 1] || {};
	setAttrs(`${CONTENT}`)(get(CONTENT).map((el) => [...el, schema.defaultContent]));
	setAttrs(`${TECHNICAL}.${COMPONENT}`)([
		...get(`${TECHNICAL}.${COMPONENT}`),
		{
			...schema.defaultTechnical,
			y: technicalSection.h,
		},
	]);
	setAttrs(`${TECHNICAL}.${SECTION}.h`)(schema.defaultTechnical.h + technicalSection.h);
	setAttrs(`${CLASS}.${COMPONENT}`)([...get(`${CLASS}.${COMPONENT}`), schema.defaultClass]);
	setAttrs(`${STYLE}.${COMPONENT}`)([...get(`${STYLE}.${COMPONENT}`), schema.defaultStyle]);
	setAttributes({ technical: { ...technical, i: i++ } });
	setGridMode((prev) => false);
};

export const onClickComponent = (component, componentI) => {
	setAttributes({
		selectedElement: { component, componentI },
	});
};
