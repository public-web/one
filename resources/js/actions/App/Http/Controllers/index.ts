import Auth from './Auth'
import DashboardController from './DashboardController'
import ActivityLogController from './ActivityLogController'
import UserController from './UserController'
import Settings from './Settings'
import PasswordChangeController from './PasswordChangeController'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    DashboardController: Object.assign(DashboardController, DashboardController),
    ActivityLogController: Object.assign(ActivityLogController, ActivityLogController),
    UserController: Object.assign(UserController, UserController),
    Settings: Object.assign(Settings, Settings),
    PasswordChangeController: Object.assign(PasswordChangeController, PasswordChangeController),
}

export default Controllers