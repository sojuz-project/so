import { withState } from '@wordpress/compose';

export default withState({
	showSources: true,
	showResults: false,
	currentQuery: {
		gql: '',
		name: '',
		fields: [],
	},
	gqls: {},
	lastBlock: undefined,
});
