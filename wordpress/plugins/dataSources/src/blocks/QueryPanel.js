import { PanelBody, PanelRow, Button } from '@wordpress/components'
import { makeRequest } from '../helpers/data';

const QueryPanel = ({ returnData, sources, show}) => {
    const actions = Object.keys(sources).map((key, id) => (
        <Button
            isPrimary
            onClick={async () => returnData(await makeRequest(key), key)}
            key={`GQL${id}`}
        >
            {key}
        </Button>
    ))
    return (
        <PanelBody
            title="Data source"
            icon="welcome-widgets-menus"
            opened={show}
        >
            <PanelRow>
                {actions}
            </PanelRow>
        </PanelBody>
    )
}

export default QueryPanel