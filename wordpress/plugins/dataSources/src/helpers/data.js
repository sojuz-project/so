import ApolloClient, { gql } from 'apollo-boost';
import gqls from '../queries';
import { select } from '@wordpress/data';
import { compSchema } from '../schema';
let i = 0;

// eslint-disable-next-line complexity
export const traverse = ({ addSource, subscribtion: { currentBlockId } = {} }, ob, name = '') => {
	const plural = name.slice(-1) == 's';
	console.log('OD', ob);
	if (plural) {
		if (Array.isArray(ob)) {
			const toStore = {
				data: ob,
				query: name,
				clientId: currentBlockId,
			};
			addSource(toStore);
			const keys = Object.keys(ob[0]);
			let index;
			if (ob[0].post_meta) {
				index = keys.indexOf('post_meta');
				if (index !== -1) keys.splice(index, 1);
				ob[0].post_meta.map((meta) => {
					keys.push(`post_meta/${meta.meta_key}`);
				});
			}
			index = keys.indexOf('__typename');
			if (index !== -1) keys.splice(index, 1);
			return keys;
		} else {
			return traverse({ addSource, currentBlockId }, ob[Object.keys(ob)[0]], name);
		}
	} else {
		const resKeys = Object.keys(ob).pop();
		return ob[resKeys] ? Object.keys(ob[resKeys]) : [];
	}
};
/* tutaj wysyłamy do gutenberga */
export const sendToList = (
	{ subscribtion: { currentBlock = false } = {}, updateBlock, gql, queryName, fields },
	item
) => {
	if (!currentBlock) {
		alert('No block selected!\nSelect block first');
		return;
	}
	if (currentBlock.name == 'sojuz/block-grid-container') {
		const ob = currentBlock.attributes;
		const componentI = ob.selectedElement.componentI;
		ob.technical.component[componentI].mapQL = item;
		ob.technical.block.dataSources = gql;
		ob.technical.block.queryName = queryName;
		ob.technical.block.fields = fields;
		ob.data.block.dataSources = gql;
		if (ob.data.component[componentI]) {
			ob.data.component[componentI].mapQL = item;
		} else {
			ob.data.component[componentI] = { mapQL: item };
		}
		updateBlock(currentBlock.clientId, ob);
		// fix to refresh
		focus(currentBlock.clientId);
	} else {
		currentBlock.attributes.content += item;
		focus(currentBlock.clientId);
	}
};

const apolloClient = new ApolloClient({
	uri: '/graphql',
});

export const makeRequest = async (queryName, vars = {}) => {
	var query = gqls[queryName] ? gqls[queryName] : queryName;
	// const permalink = select('core/editor').getPermalinkParts();
	// query = query.replace('%self%', permalink.postName);
	if (query.search('%self%') >= 0) {
		return {
			Post: {
				id: 0,
				post_title: '',
				post_content: '',
				post_excerpt: '',
				post_status: '',
				post_type: '',
				post_name: '',
				// post_parent: 0,
				post_date: '',
				// menu_order: 0,
				// layout: {},
				thumbnail: {},
				categories: [],
				post_meta: [],
				author: {},
				// blocks: [],
				// dataSources: {},
				likes: 0,
				related: [],
				liked: false,
				type: '',
			},
		};
	} else {
		return await apolloClient
			.query({
				query: gql`
					${query}
				`,
				variables: vars,
			})
			.then((data) => data.data);
	}
};

export default () => {
	throw new Error('This is not ment to be used standalone!');
};
