class UserCard extends React.Component {
    constructor(props) {
        super(props);
    }
    render() {
        return React.createElement(
            "div",
            { className: "col s12 m6" },
            React.createElement(
                "div",
                { className: "card" },
                React.createElement(
                    "div",
                    { className: "card-image" },
                    React.createElement("img", { src: "https://materializecss.com/images/sample-1.jpg" }),
                    React.createElement(
                        "span",
                        { className: "card-title" },
                        this.props.user.user_data.nombre,
                        " ",
                        this.props.user.user_data.apellido
                    ),
                    React.createElement(
                        "a",
                        { className: "btn-floating halfway-fab waves-effect waves-light red", href: BASEURL + 'admin/user/ver/' + this.props.user.id },
                        React.createElement(
                            "i",
                            { className: "material-icons" },
                            "visibility"
                        )
                    )
                ),
                React.createElement(
                    "div",
                    { className: "card-content" },
                    React.createElement(
                        "p",
                        null,
                        this.props.user.role,
                        React.createElement("br", null),
                        this.props.user.lastseen
                    )
                )
            )
        );
    }
}

class Welcome extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            users: []
        };
    }

    render() {
        return React.createElement(
            "div",
            null,
            this.state.users.map(user => React.createElement(UserCard, { user: user, key: user.id }))
        );
    }

    componentDidMount() {
        DEBUGMODE ? console.log('component mount!') : false;
        // Make a request for a user with a given ID
        axios.get(BASEURL + 'api/v1/user').then(response => {
            DEBUGMODE ? console.log(response) : false;
            const users = response.data;
            this.setState({
                users
            });
        }).catch(function (error) {
            // handle error
            console.log(error);
        }).finally(function () {
            // always executed
        });
    }

    componentWillUnmount() {}

}

const element = React.createElement(Welcome, { name: "Sara" });
ReactDOM.render(element, document.getElementById('root'));