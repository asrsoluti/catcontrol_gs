            </main>
        </div>
    </div>
    
    <script>
        // Confirmação de exclusão
        function confirmDelete(message) {
            return confirm(message || 'Tem certeza que deseja excluir?');
        }
        
        // Fechar alertas automaticamente após 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.bg-green-100, .bg-red-100');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
        
        // Máscara de CPF/CNPJ
        function maskCpfCnpj(input) {
            let value = input.value.replace(/\D/g, '');
            
            if (value.length <= 11) {
                // CPF: 000.000.000-00
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            } else {
                // CNPJ: 00.000.000/0000-00
                value = value.replace(/^(\d{2})(\d)/, '$1.$2');
                value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            }
            
            input.value = value;
        }
        
        // Máscara de telefone
        function maskPhone(input) {
            let value = input.value.replace(/\D/g, '');
            
            if (value.length <= 10) {
                // (00) 0000-0000
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            } else {
                // (00) 00000-0000
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            
            input.value = value;
        }
        
        // Máscara de CEP
        function maskCep(input) {
            let value = input.value.replace(/\D/g, '');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            input.value = value;
        }
        
        // Formatação de moeda
        function maskMoney(input) {
            let value = input.value.replace(/\D/g, '');
            value = (value / 100).toFixed(2);
            value = value.replace('.', ',');
            value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            input.value = 'R$ ' + value;
        }
        
        // Busca com autocomplete
        function setupAutocomplete(inputId, dataUrl, onSelect) {
            const input = document.getElementById(inputId);
            if (!input) return;
            
            let debounceTimer;
            
            input.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                
                debounceTimer = setTimeout(() => {
                    const term = this.value;
                    
                    if (term.length < 3) return;
                    
                    fetch(dataUrl + '?term=' + encodeURIComponent(term))
                        .then(response => response.json())
                        .then(data => {
                            // Implementar exibição de resultados
                            console.log(data);
                        });
                }, 300);
            });
        }
    </script>
</body>
</html>