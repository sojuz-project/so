const config = {
	attrs: {
		gridCss: {
			type: 'string',
		},
		data: {
			type: {},
		},
	},
	getAttrs: ({
		setAttributes,
		attributes: { gridCss, data } = {},
	} = {}) => ({ gridCss, data, setAttributes }),
};

const GridCss = ({
	gridCss = 'default',
	data = {},
	setAttributes = () => null,
}) => (
	<div>
		<select value={gridCss} onChange={({ target }) => setAttributes({ gridCss: target.value })}>
				{data.map(item => (
					<option value={item.value}>{item.label}</option>
				))}
		</select>
	</div>
);

export const GridCssCustom = (key) => ({
	config: {
		attrs: {
			[key]: {
				type: 'string',
			},
		},
		getAttrs: ({
			setAttributes,
			attributes = {},
		} = {}) => ({ [key]: attributes.key, setAttributes }),
	},
	Comp: (props) => (
		<GridCss
			gridCss={props[key]}
			data={props.data}
			setAttributes={({ gridCss: c }) => props.setAttributes({ [key]: c })}
		/>
	),
})

export default GridCss;
export {
	config,
};
