import React from 'react'
import { subscribe, select } from '@wordpress/data';

export const theSubscription = (WrappedComponent) => {
	return class Comp extends React.Component {
		constructor(props) {
			super(props);
			this.state = {
				unsubscribe: undefined,
				currentBlock: undefined,
				currentBlockId: undefined,
			}
		}
		componentDidMount() {
			const self = this;
			this.setState({
				unsubscribe: subscribe(() => {
					self.setState({
						currentBlock: select('core/editor').getSelectedBlock(),
						currentBlockId: select('core/editor').getSelectedBlockClientId(),
					});
				}),
			})
		}
		componentWillUnmount() {
			this.state.unsubscribe();
		}
		render() {
			return <WrappedComponent {...this.props} subscribtion={this.state} />
		}
	}
};
