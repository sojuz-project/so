import { withSelect } from '@wordpress/data';

export default withSelect((select) => {
	return {
		postMeta: select('core/editor').getEditedPostAttribute('meta'), // Deprecated
		blocks: select('core/editor').getBlocks(),
	};
});
