class UserCard extends React.Component {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <div className="col s12 m6">
                <div className="card">
                    <div className="card-image">
                        <img src="https://materializecss.com/images/sample-1.jpg" />
                        <span className="card-title">{this.props.user.user_data.nombre} {this.props.user.user_data.apellido}</span>
                        <a className="btn-floating halfway-fab waves-effect waves-light red" href={BASEURL + 'admin/user/ver/' + this.props.user.id}>
                            <i className="material-icons">visibility</i>
                        </a>
                    </div>
                    <div className="card-content">
                        <p>
                        {this.props.user.role}
                        <br />
                        {this.props.user.lastseen}
                        </p>
                    </div>
                </div>
            </div>
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
        return (
            <div>
                {this.state.users.map(user =>
                    <UserCard user={user} key={user.id} />
                )}
            </div>
        );
    }

    componentDidMount() {
        DEBUGMODE ? console.log('component mount!') : false;
        // Make a request for a user with a given ID
        axios.get(BASEURL + 'api/v1/user')
            .then(response => {
                DEBUGMODE ? console.log(response) : false;
                const users = response.data;
                this.setState({
                    users
                });
            })
            .catch(function (error) {
                // handle error
                console.log(error);
            })
            .finally(function () {
                // always executed
            });
    }

    componentWillUnmount() {

    }

}

const element = <Welcome name="Sara" />;
ReactDOM.render(
    element,
    document.getElementById('root')
);