// Blocks
import Sidebar from './blocks/Sidebar'
import QueryPanel from './blocks/QueryPanel';
import { Button, withFocusReturn } from '@wordpress/components'
import ResultsPanel from './blocks/ResultsPanel';
import VariablesPanel from './blocks/VariablesPanel';

// Queries
import gqlsTemplate from './queries';

// WordPress
import {registerPlugin} from '@wordpress/plugins';
import { __ } from '@wordpress/i18n';
import { compose } from '@wordpress/compose';

// Helpers
import theState from './helpers/state';
import theSelect from './helpers/select';
import theDispatch from './helpers/dispatch';
import { theSubscription } from './helpers/subscribtion';
import { theStore } from './helpers/store';
import { traverse, sendToList, makeRequest } from './helpers/data'

// React
import { useEffect } from "react";

const plugin = (props) => {
  const {
    subscribtion,
    // State
    setState,
    showSources,
    showResults,
    currentQuery,
    gqls,
    lastBlock,
    // Select
    blocks,
    // Dispatch
    openGeneralSidebar,
  } = props;
  useEffect(() => {
    const newGqls = {
      ...gqlsTemplate
    }
    // Get GQLs from block schema
    blocks.map(async block => {
      const { attributes: { technical = {} } = {} } = subscribtion.currentBlock || {};
      const { block: technicalBlock = {} } = technical
      // const { attributes: { technical: { block: technicalBlock } } } = block;
      if (technicalBlock.dataSources) {
        newGqls[technicalBlock.queryName] = {
          gql: technicalBlock.dataSources,
          name: technicalBlock.queryName,
          fields: technicalBlock.fields
        }
        const tr = traverse(props, await makeRequest(technicalBlock.dataSources), technicalBlock.queryName)
      }
    });
    setState({
      gqls: newGqls
    })
  }, [blocks.length]);

  // Block based dynamic sources change
  if (subscribtion.currentBlockId && subscribtion.currentBlockId != lastBlock) {
    const { attributes: { technical = {} } } = subscribtion.currentBlock;
    const { block: technicalBlock = {} } = technical
    if (technicalBlock.dataSources) {
      const stob = {
        showSources: false,
        showResults: true,
        currentQuery: {
          gql: technicalBlock.dataSources,
          name: technicalBlock.queryName,
          fields: technicalBlock.fields
        },
        lastBlock: subscribtion.currentBlockId
      }
      setState(stob)
    }
  }

  const pushSource = (data, currentQueryName, withVars = false) => { // eslint-disable-line
    const traversed = traverse(props, data, currentQueryName);
    const stob = {
      showSources: false,
      showResults: true,
      currentQuery: {
        gql: (withVars) ? withVars: gqls[currentQueryName],
        name: currentQueryName,
        fields: traversed,
      }
    };
    setState(stob)
  }

  const queryPanel = (showSources) ? (
    <QueryPanel
      returnData={pushSource} // eslint-disable-line
      sources={gqls}
      show={showSources}
    />
  ) : '';
  const resultsPanel = (showResults) ? (
    <>
    <ResultsPanel
      currentQuery={currentQuery} // eslint-disable-line
      show={showResults}
      higher={{...props, sendToList, activeBlock: theSubscription.activeBlock}}
      change={() => {
        const stob = {
          showResults: false,
          showSources: true,
          currentQuery: {
            gql: '',
            name: '',
            fields: [],
          },
        };
        setState(stob);
      }}
    />
    <VariablesPanel
      currentQuery={currentQuery}
      returnData={pushSource}
    />
    </>
  ) : ''
  return (
    <Sidebar>
      <Button
          className="full-width" // eslint-disable-line
          isDefault
          isLarge
          onClick={openGeneralSidebar}
      >
          ← {__('Back to main block settings')}
      </Button>
      {queryPanel}
      {resultsPanel}
    </Sidebar>
  )
};

const armed = compose(
  theState,
  theSelect,
  theDispatch,
  theSubscription,
) ( plugin )

registerPlugin('data-sources-plugin', { render: armed });
