import { withDispatch } from '@wordpress/data';

export default withDispatch((dispatch) => ({
	addSource: ({ query, data, clientId }) => dispatch('dataSourcesStore').addSource(query, data, clientId),
	delSource: (clientId) => dispatch('dataSourcesStore').removeSource(clientId),
	populateSources: (srcs, data = {}) => dispatch('dataSourcesStore').populate(srcs, data),
	focus: (clientId) => {
		dispatch('core/editor').selectBlock();
		window.setTimeout(() => {
			dispatch('core/editor').selectBlock(clientId);
		}, 1);
	},
	openGeneralSidebar: () => dispatch('core/edit-post').openGeneralSidebar(),
	updateBlock: (clientId, object) => dispatch('core/block-editor').updateBlock(clientId, object),
}));
