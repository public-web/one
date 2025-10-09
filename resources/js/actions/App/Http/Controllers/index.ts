import Auth from './Auth';
import DashboardController from './DashboardController';
import PasswordChangeController from './PasswordChangeController';
import Settings from './Settings';
import UserController from './UserController';

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    DashboardController: Object.assign(DashboardController, DashboardController),
    UserController: Object.assign(UserController, UserController),
    Settings: Object.assign(Settings, Settings),
    PasswordChangeController: Object.assign(PasswordChangeController, PasswordChangeController),
};

export default Controllers;
