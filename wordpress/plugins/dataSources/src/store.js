// const { apiFetch } = wp;
const { registerStore } = wp.data;

const DEFAULT_STATE = {
	dataSources: [],
};

const actions = {
	add_source(src) {
		return {
			type: 'ADD_SOURCE',
			src,
		};
	},
	populate(src) {
		return {
			type: 'POPULATE_STORE',
			src,
		};
	},
	clearSources(clientId) {
		return {
			type: 'CLEAR_SOURCES',
			clientId,
		};
	},
};

export default registerStore('dataSourcesStore', {
	reducer(state = DEFAULT_STATE, action) {
		switch (action.type) {
			case 'ADD_SOURCE':
				return {
					...state,
					dataSources: { ...state.dataSources, ...action.src },
				};
			case 'POPULATE_STORE':
				return {
					...state,
					dataSources: { ...action.src },
				};
			case 'CLEAR_SOURCES':
				delete state.dataSources[action.clientId];
				console.log('state', state);
				return state;
		}
		return state;
	},

	actions,

	selectors: {
		getData(state) {
			return state.dataSources;
		},
		getCurrentData(state) {
			const currentBlock = wp.data.select('core/editor').getSelectedBlock();
			if (currentBlock) {
				return state.dataSources[currentBlock.clientId];
			} else {
				return [];
			}
		},
	},

	// controls: {
	// 	FETCH_FROM_API( action ) {
	// 		return apiFetch( { path: action.path } );
	// 	},
	// },

	// resolvers: {
	// 	* getPrice( item ) {
	// 		const path = '/wp/v2/prices/' + item;
	// 		const price = yield actions.fetchFromAPI( path );
	// 		return actions.setPrice( item, price );
	// 	},
	// },
});
