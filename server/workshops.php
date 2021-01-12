<?php
header("Content-Type: application/json; charset=UTF-8");
echo '
{
  "dias": [
    {
      "dia": "24 de fevereiro",
      "workshops": [
        {
          "wid": "ws1",
          "nome": "Introdução à linha de comandos Linux",
          "horas": "10:00 - 12:00",
          "dado_por": "Ângulo Sólido",
          "imagem": "ws1.png",
          "cheio": false,
          "inscrito": false
        },
        {
          "wid": "ws2",
          "nome": "Reinforcement Learning",
          "horas": "14:00 - 16:00",
          "dado_por": "NEEC",
          "imagem": "ws2.png",
          "cheio": true,
          "inscrito": false
        },
        {
          "wid": "ws3",
          "nome": "Flutter",
          "horas": "16:00 - 18:00",
          "dado_por": "NEEC",
          "imagem": "ws3.png",
          "cheio": true,
          "inscrito": true
        }
      ]
    },
    {
      "dia": "25 de fevereiro",
      "workshops": [
        {
          "wid": "ws1",
          "nome": "Introdução à linha de comandos Linux",
          "horas": "10:00 - 12:00",
          "dado_por": "Ângulo Sólido",
          "imagem": "ws1.png",
          "cheio": false,
          "inscrito": false
        },
        {
          "wid": "ws2",
          "nome": "Reinforcement Learning",
          "horas": "14:00 - 16:00",
          "dado_por": "NEEC",
          "imagem": "ws2.png",
          "cheio": true,
          "inscrito": false
        },
        {
          "wid": "ws3",
          "nome": "Flutter",
          "horas": "16:00 - 18:00",
          "dado_por": "NEEC",
          "imagem": "ws3.png",
          "cheio": false,
          "inscrito": true
        }
      ]
    }
  ]
}';