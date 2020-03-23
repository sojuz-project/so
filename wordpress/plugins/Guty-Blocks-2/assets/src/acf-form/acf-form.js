import './acf-form.view.scss';
import './acf-form.editor.scss';
import { useEffect } from 'react';

const { registerBlockType } = wp.blocks;

const { InspectorControls, RichText } = wp.editor;

registerBlockType('sojuz/acf-form', {
  title: 'acf-form',
  icon: 'clipboard',
  category: 'layout',

  supports: {
    align: ['full', 'wide'],
  },

  attributes: {
    align: {
      type: 'string',
    },
    form: {
      type: 'string',
      default: '',
    },
    endpoint: {
      type: 'string',
      default: '',
    },
    loginOnly: {
      type: 'boolean',
    },
    logoutOnly: {
      type: 'boolean',
    },
    labels: {
      type: 'boolean',
    },
    title: {
      type: 'boolean',
    },
  },

  edit(props) {
    const { className, setAttributes } = props;
    const [state, setState] = React.useState({
      opts: [],
    });

    useEffect(() => {
      wp.ajax
        .post({
          action: 'acf_schema',
        })
        .then((e) => {
          const opts = Object.keys(e).map((k) => (
            <option key={k} value={k} selected={k == props.attributes.form}>
              {e[k]}
            </option>
          ));
          setState({ opts });
        });
    }, [props.attributes.form]);

    // const {  } = props.attributes;

    const handleChange = (e) => {
      // e.persist()
      // console.log(e, e.target, e.target.id, e.target.value)
      setAttributes({
        [e.target.id]: 'checkbox' == e.target.type ? e.target.checked : e.target.value,
      });
    };

    return [
      <InspectorControls>
        <div style={{ padding: '1em 0' }}>Options</div>
      </InspectorControls>,
      <div className={className}>
        {/* {JSON.stringify(props.attributes)} */}
        <select id="form" onChange={handleChange}>
          <option>Choose one</option>
          {state.opts}
        </select>
        <input
          type="text"
          onChange={handleChange}
          placeholder="endpoint"
          id="endpoint"
          value={props.attributes.endpoint}
        />
        <br />
        <label htmlFor="labels">
          <input type="checkbox" onChange={handleChange} id="labels" checked={props.attributes.labels} value={true} />
          Show field labels
        </label>
        <label htmlFor="title">
          <input type="checkbox" onChange={handleChange} id="title" checked={props.attributes.title} value={true} />
          Show form title
        </label>
        <label htmlFor="loginOnly">
          <input
            type="checkbox"
            onChange={handleChange}
            id="loginOnly"
            checked={props.attributes.loginOnly}
            value={true}
          />
          Login only
        </label>
        <label htmlFor="logoutOnly">
          <input
            type="checkbox"
            onChange={handleChange}
            id="logoutOnly"
            checked={props.attributes.logoutOnly}
            value={true}
          />
          Logout only
        </label>
      </div>,
    ];
  },

  save(props) {
    // const className = getBlockDefaultClassName('sojuz/acf-form'); // For use with say, BEM
    // const {  } = props.attributes;

    return <div>acf-form</div>;
  },
});
