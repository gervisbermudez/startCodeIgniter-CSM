class UserCard extends React.Component {
    constructor(props) {
        super(props);
    }
    render() {
        let cardImage;
        if(this.props.user.user_data.avatar){
            cardImage = <img src={BASEURL + 'public/img/profile/' + this.props.user.username + '/' + this.props.user.user_data.avatar} />
        }else{
            cardImage =  <img src="https://materializecss.com/images/sample-1.jpg" />
        }
        return (
            <div className="col s12 m4">
                <div className="card user-card">
                        <div className="card-image">
                            <div className="card-image-container">
                            {cardImage}
                            </div>
                            <span className="card-title">{this.props.user.user_data.nombre} {this.props.user.user_data.apellido}</span>
                            <a className="btn-floating halfway-fab waves-effect waves-light" href={BASEURL + 'admin/user/ver/' + this.props.user.id}>
                                <i className="material-icons">visibility</i>
                            </a>
                    </div>
                    <div className="card-content">
                        <div>
                        <div className="card-info">
                            <i className="material-icons">account_box</i> {this.props.user.role}
                            <br />
                        </div>
                        <div className="card-info">
                            <i className="material-icons">access_time</i> {this.props.user.lastseen}
                            <br />
                        </div>
                        <div className="card-info">
                            <i className="material-icons">local_phone</i> {this.props.user.user_data.telefono}
                            </div>
                        </div>
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