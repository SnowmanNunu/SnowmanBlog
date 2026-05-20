# 使用 Claude API 构建生产级应用：从入门到上线

Claude 系列模型（尤其是 Claude 4 Sonnet/Opus）在代码理解、长文本处理和推理能力上表现突出。本文从零开始，讲解如何用 Claude API 构建一个可直接上线的 AI 应用。

---

## 环境准备

### 1. 获取 API Key

访问 [console.anthropic.com](https://console.anthropic.com) 注册账号，创建 API Key。新账号有免费额度，足够开发和测试。

### 2. 安装 SDK

```bash
npm install @anthropic-ai/sdk
# 或
pip install anthropic
```

---

## 基础调用

### Node.js 示例

```typescript
import Anthropic from '@anthropic-ai/sdk';

const client = new Anthropic({
  apiKey: process.env.ANTHROPIC_API_KEY,
});

const msg = await client.messages.create({
  model: 'claude-sonnet-4-6-20251022',
  max_tokens: 1024,
  messages: [
    { role: 'user', content: '用 Python 写一个快速排序' }
  ],
});

console.log(msg.content[0].text);
```

### 关键参数说明

| 参数 | 说明 |
|------|------|
| `model` | 模型 ID，Sonnet 性价比最高，Opus 能力最强 |
| `max_tokens` | 最大输出 Token 数，必须设置 |
| `temperature` | 创造性控制（0-1），代码生成建议 0.1-0.3 |
| `system` | 系统提示，定义全局角色和行为约束 |

---

## 系统提示的设计

系统提示（System Prompt）是控制模型行为的"宪法"。一个好的系统提示能显著提升输出质量。

```typescript
const response = await client.messages.create({
  model: 'claude-sonnet-4-6-20251022',
  system: `你是一位资深 Python 后端工程师，擅长 Django 和 FastAPI。
规则：
1. 所有代码必须包含类型注解
2. 必须处理异常和边界情况
3. 优先使用标准库，其次才是第三方库
4. 输出格式：先给完整代码，再逐段解释`,
  messages: [{ role: 'user', content: '写一个用户认证的 JWT 中间件' }],
  max_tokens: 2048,
});
```

---

## 流式输出（Streaming）

生产环境必须使用流式输出，降低用户等待感：

```typescript
const stream = await client.messages.create({
  model: 'claude-sonnet-4-6-20251022',
  max_tokens: 2048,
  messages: [{ role: 'user', content: '讲一个关于程序员的笑话' }],
  stream: true, // 启用流式
});

for await (const chunk of stream) {
  if (chunk.type === 'content_block_delta') {
    process.stdout.write(chunk.delta.text);
  }
}
```

配合 SSE（Server-Sent Events）可实现 ChatGPT 式的打字机效果。

---

## 工具调用（Tool Use）

Claude 支持 Function Calling，让模型主动调用外部工具：

```typescript
const tools = [
  {
    name: 'get_weather',
    description: '获取指定城市的天气',
    input_schema: {
      type: 'object',
      properties: {
        city: { type: 'string', description: '城市名称' },
      },
      required: ['city'],
    },
  },
];

const msg = await client.messages.create({
  model: 'claude-sonnet-4-6-20251022',
  max_tokens: 1024,
  tools,
  messages: [{ role: 'user', content: '北京今天天气怎么样？' }],
});

// 模型决定调用工具
if (msg.stop_reason === 'tool_use') {
  const toolCall = msg.content.find(c => c.type === 'tool_use');
  const weather = await getWeather(toolCall.input.city);

  // 把工具结果回传给模型
  const final = await client.messages.create({
    model: 'claude-sonnet-4-6-20251022',
    max_tokens: 1024,
    tools,
    messages: [
      { role: 'user', content: '北京今天天气怎么样？' },
      { role: 'assistant', content: msg.content },
      { role: 'user', content: `[tool_result] ${JSON.stringify(weather)}` },
    ],
  });
}
```

---

## 长文本处理

Claude 4 支持 **200K Token** 的上下文窗口，可以一次性处理整本书或大量代码库。

**最佳实践：**
- 使用 XML 标签包裹不同来源的内容，帮助模型区分：
  ```
  <document index="1">
    <source>api_design.md</source>
    <content>...</content>
  </document>
  ```
- 长文本末尾放置核心指令（LLM 对末尾内容注意力更高）
- 超出窗口时，使用 RAG 分段检索而非硬塞

---

## 成本控制策略

| 策略 | 说明 |
|------|------|
| 缓存提示（Prompt Caching）| 重复使用的系统提示可缓存，降低 90% 输入成本 |
| 模型降级 | 简单任务用 Haiku，复杂任务再用 Sonnet/Opus |
| 输出限制 | 合理设置 `max_tokens`，避免过度生成 |
| 批量处理 | 使用 Batch API，价格更低但有 24h 延迟 |

---

## 写在最后

Claude API 的接入门槛很低，但要做成生产级应用，需要在**系统提示设计、流式交互、错误处理、成本控制**四个维度下功夫。

下一篇，我们将探讨 **RAG（检索增强生成）**——如何让 LLM 基于私有知识库回答问题。
