import Auth from './Auth'
import DashboardController from './DashboardController'
import ActivityLogController from './ActivityLogController'
import UserController from './UserController'
import RoleController from './RoleController'
import PermissionController from './PermissionController'
import ArticuloController from './ArticuloController'
import Settings from './Settings'
import PasswordChangeController from './PasswordChangeController'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    DashboardController: Object.assign(DashboardController, DashboardController),
    ActivityLogController: Object.assign(ActivityLogController, ActivityLogController),
    UserController: Object.assign(UserController, UserController),
    RoleController: Object.assign(RoleController, RoleController),
    PermissionController: Object.assign(PermissionController, PermissionController),
    ArticuloController: Object.assign(ArticuloController, ArticuloController),
    Settings: Object.assign(Settings, Settings),
    PasswordChangeController: Object.assign(PasswordChangeController, PasswordChangeController),
}

export default Controllers