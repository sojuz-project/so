import { registerStore, select, dispatch } from '@wordpress/data';

const DEFAULT_STATE = {
	activeBlock: {},
	dssSet: false,
	queries: {},
	data: {},
	dataSources: {}, // Deprecated in favor to data
	sidebarOpen: false,
};
const ADD_SOURCE = 'ADD_SOURCE';
const DEL_SOURCE = 'DELETE_SOURCE';
const POPULATE = 'POPULATE_STORE';
const TOGGLE_SIDEBAR = 'TOGGLE_SIDEBAR';

const actions = {
	/**
	 * Adds source mapping (clientId => dataSource) to global store
	 * @param {Object} query object defining query (from state)
	 * @param {Object} data object containing sources result
	 * @param {String} clientId ID of the Client
	 */
	addSource(query, data, clientId) {
		return {
			type: ADD_SOURCE,
			query,
			data,
			clientId,
		};
	},
	/**
	 * Removes source mapping from global store
	 * @param {String} clientId block ID
	 */
	removeSource(clientId) {
		return {
			type: DEL_SOURCE,
			clientId,
		};
	},
	/**
	 * DEPRECATED in favor to addSource!
	 *
	 * Adds source mapping (clientId => dataSource) to global store
	 * @param {Object} sourceObject
	 */
	add_source(sourceObject) {
		return {
			type: ADD_SOURCE,
			sourceObject,
		};
	},
	/**
	 * DEPRECATED in favor to removeSource!
	 *
	 * Removes source mapping from global store
	 * @param {String} clientId block ID
	 */
	remove_source(clientId) {
		return {
			type: DEL_SOURCE,
			clientId,
		};
	},
	/**
	 * Replaces global store dataSources with given mapping object (clientId => dataSource)
	 * @param {Object} sources new sources mapping object
	 * @param {Object} data data values object
	 */
	populate(sources, data = {}) {
		const queries = sources; // Backwards compatibility

		return {
			type: POPULATE,
			queries,
			sources,
			data,
		};
	},
	/**
	 * Roggles dataSources Sidebar
	 */
	toggle() {
		if (select('dataSourcesStore').isOpen()) {
			dispatch('core/edit-post').openGeneralSidebar('edit-post/block');
		} else {
			dispatch('core/edit-post').openGeneralSidebar('data-sources-plugin/dataSources');
		}
		return {
			type: TOGGLE_SIDEBAR,
		};
	},
};

const theStore = () => {
	registerStore('dataSourcesStore', {
		
		reducer(state = DEFAULT_STATE, action) {
			switch (action.type) {
				case ADD_SOURCE:
					const queries = { ...state.queries };
					queries[action.clientId] = action.query;
					const data = { ...state.data };
					data[action.clientId] = action.data;
					const newState = {
						...state,
						data,
						queries,
						dataSources: { ...state.dataSources, ...action.sourceObject }, // DEPRECATED in favor to data!
					};
					// console.log('nState >>> ', newState);
					return newState;
				case POPULATE:
					return {
						...state,
						data: { ...action.data },
						queries: { ...action.queries },
						dataSources: { ...action.sources }, // DEPRECATED in favor to data!
					};
				case DEL_SOURCE:
					delete state.dataSources[action.clientId]; // DEPRECATED
					delete state.data[action.clientId];
					delete state.queries[clientId];
					return state;
				case TOGGLE_SIDEBAR:
					const ret = {
						...state,
					};
					if (!state.dssSet) {
						ret.activeBlock = select('core/editor').getSelectedBlock();
						ret.sidebarOpen = !state.sidebarOpen;
						ret.dssSet = true;
					} else {
						ret.activeBlock = {};
						ret.sidebarOpen = !state.sidebarOpen;
						ret.dssSet = false;
					}
					// console.log('activeB', ret);
					return ret;
			}
			return state;
		},
		actions,
		selectors: {
			/**
			 * Returns the whole dataSources object
			 * @param {*} state Current state
			 */
			getData(state) {
				// return state.dataSources;
				return state.data;
			},
			/**
			 * Returns stored queries
			 * @param {*} state Current state
			 */
			getQueries(state) {
				return state.queries;
			},
			/**
			 * Returns currently selected block
			 * @param {*} state Current state
			 */
			getSelectedBlock(state) {
				return state.activeBlock;
			},
			/**
			 * Returns dataSources object for selected block (or empty array if none selected)
			 * @param {*} state Current state
			 */
			getCurrentData(state) {
				const currentBlock = select('core/editor').getSelectedBlock();
				if (currentBlock) {
					return state.data[currentBlock.clientId];
				} else {
					return [];
				}
			},
			isOpen(state) {
				const isOpened =
					select('core/edit-post').isPluginSidebarOpened &&
					select('core/edit-post').getActiveGeneralSidebarName() == 'data-sources-plugin/dataSources';
				return state.sidebarOpen || isOpened;
			},
			isDataSet(state) {
				return state.dssSet;
			},
		},
	});
};

export default theStore();
