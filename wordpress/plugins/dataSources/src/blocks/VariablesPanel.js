import { Placeholder, PanelBody, PanelRow, TextControl, Button } from '@wordpress/components'
import { withState } from '@wordpress/compose';
import { __ } from '@wordpress/i18n'
import { gql } from 'apollo-boost'
import { print } from 'graphql/language/printer'
import { makeRequest } from '../helpers/data';

const VariablesPanel = withState({
  values: {},
})((props) => {
  let results;
  const { gql:queryString, name } = props.currentQuery;
  const placeholder = queryString.match(/\%([^\%]*)\%/)
  if (placeholder) {
    return []
  }
  const { setState } = props;
  if (queryString) {
    const { values } = props;
    const query = gql`${queryString}`
    // console.log('q', query);
    const { definitions: [ { variableDefinitions: schema} ] } = query;
    const theState = {
      values,
    };
    results = schema.map((field) => {
      const {defaultValue: defaultValueOb, variable: {name: { value: variable}} } = field;
      let defaultValue;
      let helpText;
      if (defaultValueOb.kind == 'ListValue') {
        helpText = __('Items separated by coma');
        defaultValue = defaultValueOb.values.map(val => val.value).join(',');
      } else {
        defaultValue = defaultValueOb.value;
      }
      // console.log('stype', typeof(theState.values[variable]))
      if (typeof(theState.values[variable]) === 'undefined') {
        theState.values[variable] = defaultValue;
      }
      // console.log('st', theState)
      return (
        <TextControl
          label={variable}
          value={(typeof(props.values[variable]) !== 'undefined' ) ? props.values[variable] : defaultValue}
          help={helpText}
          onChange={(value) => {
            const state = {...theState};
            state.values[variable] = value
            // console.log('val', value, state)
            return setState(state)
          }}
          key={variable}
        />
      )
    });
    if (Array.isArray(results) && results.length) {
      results.push(
        <Button
          isPrimary
          key="updateBtn"
          onClick={async () => {
            const qq = gql`${gql}`;
            const defaultValues = qq.definitions[0].variableDefinitions;
            const updates = defaultValues.map((element) => {
              element.defaultValue.value = props.values[element.variable.name.value]
              return element
            })
            qq.definitions[0].variableDefinitions = updates
            const parsed = print(qq);
            const query = (props.qName.includes('custom')) ? gql : name;
            props.returnData(await makeRequest(query, values), name+' custom', parsed)
          }}
        >
          {__('Update')}
        </Button>
      );
    } else {
      results = (
        <Placeholder
          icon="admin-settings"
          label={ __("No variables to change") }
          instructions={ __('Selected query has no variables defined') }
        />
      )
    }
  }
  return (
      <PanelBody
          title={__('Query variables')}
          icon="admin-settings"
          initialOpen={false}
      >
          <PanelRow>
              {results}
          </PanelRow>
      </PanelBody>
  )
})

export default VariablesPanel