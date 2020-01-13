class UserCard extends React.Component {
    constructor(props) {
        super(props);
    }
    render() {
        let cardImage;
        if (this.props.user.user_data.avatar) {
            cardImage = React.createElement('img', { src: BASEURL + 'public/img/profile/' + this.props.user.username + '/' + this.props.user.user_data.avatar });
        } else {
            cardImage = React.createElement('img', { src: 'https://materializecss.com/images/sample-1.jpg' });
        }
        return React.createElement(
            'div',
            { className: 'col s12 m4' },
            React.createElement(
                'div',
                { className: 'card user-card' },
                React.createElement(
                    'div',
                    { className: 'card-image' },
                    React.createElement(
                        'div',
                        { className: 'card-image-container' },
                        cardImage
                    ),
                    React.createElement(
                        'span',
                        { className: 'card-title' },
                        this.props.user.user_data.nombre,
                        ' ',
                        this.props.user.user_data.apellido
                    ),
                    React.createElement(
                        'a',
                        { className: 'btn-floating halfway-fab waves-effect waves-light', href: BASEURL + 'admin/user/ver/' + this.props.user.id },
                        React.createElement(
                            'i',
                            { className: 'material-icons' },
                            'visibility'
                        )
                    )
                ),
                React.createElement(
                    'div',
                    { className: 'card-content' },
                    React.createElement(
                        'div',
                        null,
                        React.createElement(
                            'div',
                            { className: 'card-info' },
                            React.createElement(
                                'i',
                                { className: 'material-icons' },
                                'account_box'
                            ),
                            ' ',
                            this.props.user.role,
                            React.createElement('br', null)
                        ),
                        React.createElement(
                            'div',
                            { className: 'card-info' },
                            React.createElement(
                                'i',
                                { className: 'material-icons' },
                                'access_time'
                            ),
                            ' ',
                            this.props.user.lastseen,
                            React.createElement('br', null)
                        ),
                        React.createElement(
                            'div',
                            { className: 'card-info' },
                            React.createElement(
                                'i',
                                { className: 'material-icons' },
                                'local_phone'
                            ),
                            ' ',
                            this.props.user.user_data.telefono
                        )
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
            'div',
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

const element = React.createElement(Welcome, { name: 'Sara' });
ReactDOM.render(element, document.getElementById('root'));