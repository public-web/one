import { mount, flushPromises } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import UserList from '../Components/UserList.vue';

// Mock de fetch global
global.fetch = vi.fn();

describe('UserList Component', () => {
    beforeEach(() => {
        // Limpiar mocks antes de cada test
        vi.clearAllMocks();
    });

    it('muestra loading y luego los datos', async () => {
        const mockUsers = [
            { id: 1, name: 'Test User', email: 'test@test.com' },
        ];
        
        (global.fetch as any).mockResolvedValueOnce({
            ok: true,
            json: async () => mockUsers,
        });
        
        const wrapper = mount(UserList);
        
        // Esperar a que termine de cargar
        await flushPromises();
        
        // Ahora debería mostrar los usuarios
        expect(wrapper.text()).toContain('Test User');
        expect(wrapper.text()).not.toContain('Cargando...');
    });

    it('carga y muestra usuarios correctamente', async () => {
        // Mock de respuesta exitosa
        const mockUsers = [
            { id: 1, name: 'Juan Carlos', email: 'juan@test.com' },
            { id: 2, name: 'María', email: 'maria@test.com' },
        ];
        
        (global.fetch as any).mockResolvedValueOnce({
            ok: true,
            json: async () => mockUsers,
        });
        
        const wrapper = mount(UserList);
        
        // Esperar a que se resuelvan las promesas
        await flushPromises();
        
        // Verificar que los usuarios se muestran
        expect(wrapper.text()).toContain('Juan Carlos');
        expect(wrapper.text()).toContain('juan@test.com');
        expect(wrapper.text()).toContain('María');
        expect(wrapper.findAll('li')).toHaveLength(2);
    });

    it('muestra error cuando la API falla', async () => {
        // Mock de respuesta con error
        (global.fetch as any).mockResolvedValueOnce({
            ok: false,
        });
        
        const wrapper = mount(UserList);
        
        await flushPromises();
        
        expect(wrapper.find('.error').exists()).toBe(true);
        expect(wrapper.text()).toContain('Error al cargar usuarios');
    });

    it('llama a la API con la URL correcta', async () => {
        (global.fetch as any).mockResolvedValueOnce({
            ok: true,
            json: async () => [],
        });
        
        mount(UserList);
        
        await flushPromises();
        
        // Verificar que se llamó a fetch con la URL correcta
        expect(global.fetch).toHaveBeenCalledWith('/api/users');
        expect(global.fetch).toHaveBeenCalledTimes(1);
    });
});
