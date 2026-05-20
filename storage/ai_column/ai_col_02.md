# MCP 协议实战：让 AI 安全地操作你的工具

当 AI Agent 需要调用外部工具时，一个关键问题浮现：如何让 LLM 知道有哪些工具可用、如何调用、参数格式是什么？**MCP（Model Context Protocol）** 协议正是为解决这一问题而生。

---

## 为什么需要 MCP

在 MCP 出现之前，每个 AI 应用都要手写工具调用逻辑：

```python
# 传统方式：硬编码工具定义
def get_weather(city: str):
    ...

def search_db(query: str):
    ...

# 手动把函数描述拼接成 prompt
system_prompt = """
你可以使用以下工具：
- get_weather(city: string)
- search_db(query: string)
"""
```

这种方式的问题很明显：
- 工具描述与实现分离，容易不同步
- 每个框架的调用格式不统一
- 安全风险难以管控（AI 可能调用危险操作）

**MCP 用一套标准化协议解决了这些问题。**

---

## MCP 是什么

MCP（Model Context Protocol）是 Anthropic 于 2024 年底推出的**开放协议**，定义了 AI 模型与外部工具之间的通信标准。它的设计哲学类似 USB 接口——统一接口，即插即用。

核心概念：
- **Server**：提供工具的端（如文件系统、数据库、搜索引擎）
- **Client**：消费工具的端（如 Claude Desktop、Cursor、自建 Agent）
- **Transport**：通信方式（stdio 本地进程 / HTTP SSE 远程服务）

---

## MCP Server 的结构

一个 MCP Server 需要实现三个核心能力：

### 1. 工具列表（Tools）
向客户端声明自己提供哪些工具，每个工具包含名称、描述、参数 Schema。

```json
{
  "tools": [
    {
      "name": "read_file",
      "description": "读取文件内容",
      "inputSchema": {
        "type": "object",
        "properties": {
          "path": { "type": "string", "description": "文件路径" }
        },
        "required": ["path"]
      }
    }
  ]
}
```

### 2. 工具调用（Call Tool）
客户端根据 LLM 的决定，调用具体工具并返回结果。

### 3. 资源列表（Resources）
可选能力，用于暴露只读数据（如日志文件、配置文件）。

---

## 实战：开发一个文件系统 MCP Server

以 Node.js 为例，基于 `@modelcontextprotocol/sdk` 开发：

```typescript
import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';

const server = new Server(
  { name: 'fs-server', version: '1.0.0' },
  { capabilities: { tools: {} } }
);

server.setRequestHandler(ListToolsRequestSchema, async () => {
  return {
    tools: [
      {
        name: 'read_file',
        description: '读取指定文件的内容',
        inputSchema: {
          type: 'object',
          properties: {
            path: { type: 'string', description: '文件绝对路径' }
          },
          required: ['path']
        }
      }
    ]
  };
});

server.setRequestHandler(CallToolRequestSchema, async (request) => {
  if (request.params.name === 'read_file') {
    const { path } = request.params.arguments;
    const content = await fs.readFile(path, 'utf-8');
    return { content: [{ type: 'text', text: content }] };
  }
});

const transport = new StdioServerTransport();
await server.connect(transport);
```

配置文件（Claude Desktop 示例）：

```json
{
  "mcpServers": {
    "filesystem": {
      "command": "node",
      "args": ["/path/to/fs-server.js"]
    }
  }
}
```

启动 Claude Desktop 后，它就能自动发现并使用 `read_file` 工具了。

---

## 安全最佳实践

MCP 的设计天然考虑了安全性：

1. **权限隔离**：Server 以独立进程运行，与 LLM 隔离
2. **参数校验**：所有入参必须通过 JSON Schema 校验
3. **范围限制**：文件系统 Server 可配置允许访问的路径白名单
4. **人工确认**：敏感操作（如删除文件）可要求用户确认

---

## 生态现状

目前已有大量开源 MCP Server：

| Server | 功能 |
|--------|------|
| @modelcontextprotocol/server-filesystem | 文件读写 |
| @modelcontextprotocol/server-postgres | PostgreSQL 查询 |
| @modelcontextprotocol/server-github | GitHub API 操作 |
| @modelcontextprotocol/server-puppeteer | 浏览器自动化 |

**趋势判断**：MCP 正在逐渐成为 AI 工具调用的"事实标准"，类似 Docker 之于容器化。

---

## 写在最后

MCP 的价值不在于技术复杂度，而在于**标准化带来的互操作性**。一旦你的工具封装成 MCP Server，任何支持 MCP 的客户端（Claude、Cursor、自建 Agent）都能直接使用。

下一篇，我们将探讨 **Prompt Engineering 的进阶技巧**——如何让 LLM 更精准地理解你的意图。
