import { Placeholder, PanelBody, PanelRow, Button } from '@wordpress/components'
import { __ } from '@wordpress/i18n'

const ResultsPanel = (props) => {
    const { show, currentQuery, change, higher } = props
    const { name } = currentQuery;
    const { sendToList } = higher;

    let results = (
        <Placeholder
        icon="list-view"
        label={ __("No data detected") }
        instructions={ __('Choose valid data source from panel aboove') }
        />
    );
    if (currentQuery) {
        const { name, fields, gql } = currentQuery;
        if (fields.length) {
            const buttons = fields.map( field => (
                <Button
                    key={`f${field}`}
                    className="full-width"
                    isDefault
                    onClick={() => sendToList({
                        ...higher,
                        gql,
                        queryName: name,
                        fields: fields,
                    }, field)
                }>
                    %{field}%
                </Button>
            ));
            results = (
                <div className="resultList">
                    {buttons}
                </div>
            )
        }
    }
    const qName = (name) ? `${name}: ${__('results')}`: __('Results');
    return (
        <PanelBody
            title={qName}
            icon="list-view"
            opened={show}
        >
            <PanelRow>
                <Button
                    onClick={() => change()}
                    isPrimary
                    isLarge
                    className="full-width"
                >
                    ← {__('Change source')}
                </Button>
                {results}
            </PanelRow>
        </PanelBody>
    )
}

export default ResultsPanel