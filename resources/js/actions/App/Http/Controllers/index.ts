import Auth from './Auth'
import Api from './Api'
import DashboardController from './DashboardController'
import PreviabilizacionSocialController from './PreviabilizacionSocialController'
import ActivityLogController from './ActivityLogController'
import UserController from './UserController'
import RoleController from './RoleController'
import PermissionController from './PermissionController'
import BancoProyectoController from './BancoProyectoController'
import ArticuloController from './ArticuloController'
import Settings from './Settings'
import PasswordChangeController from './PasswordChangeController'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    Api: Object.assign(Api, Api),
    DashboardController: Object.assign(DashboardController, DashboardController),
    PreviabilizacionSocialController: Object.assign(PreviabilizacionSocialController, PreviabilizacionSocialController),
    ActivityLogController: Object.assign(ActivityLogController, ActivityLogController),
    UserController: Object.assign(UserController, UserController),
    RoleController: Object.assign(RoleController, RoleController),
    PermissionController: Object.assign(PermissionController, PermissionController),
    BancoProyectoController: Object.assign(BancoProyectoController, BancoProyectoController),
    ArticuloController: Object.assign(ArticuloController, ArticuloController),
    Settings: Object.assign(Settings, Settings),
    PasswordChangeController: Object.assign(PasswordChangeController, PasswordChangeController),
}

export default Controllers