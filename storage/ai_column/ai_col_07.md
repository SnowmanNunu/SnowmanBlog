# AI 应用的成本控制与性能优化

LLM API 按 Token 计费，一个日活 1000 用户的应用，如果设计不当，每月可能烧掉几千甚至上万元。**成本控制不是"省着用"，而是"聪明地用"**。

---

## 理解 Token 消耗

Token 是 LLM 处理文本的最小单位。中英文混合场景下，大致比例：

- 1 个汉字 ≈ 1-2 Token
- 1 个英文单词 ≈ 1-2 Token
- 系统提示 + 历史对话 + 当前输入 = 总输入 Token

**成本构成：**

```
单次请求成本 = 输入 Token × 输入单价 + 输出 Token × 输出单价
```

以 Claude Sonnet 为例（2026 年价格）：
- 输入：$3 / 1M Token
- 输出：$15 / 1M Token

一个典型的客服对话（输入 2K + 输出 500）≈ $0.0135，1000 次就是 $13.5。

---

## 成本控制策略

### 1. 提示缓存（Prompt Caching）

系统提示和固定上下文每次请求都要重复发送。**Claude 支持 Prompt Caching**，缓存后的输入价格降低 90%。

```typescript
const response = await anthropic.messages.create({
  model: 'claude-sonnet-4-6-20251022',
  max_tokens: 1024,
  system: [
    {
      type: 'text',
      text: longSystemPrompt, // 这段会被缓存
      cache_control: { type: 'ephemeral' },
    },
  ],
  messages: userMessages,
});
```

**效果**：如果系统提示占 80% Token，缓存后整体成本可降 70% 以上。

### 2. 模型分级

不是所有任务都需要最强模型：

| 任务类型 | 推荐模型 | 成本对比 |
|----------|----------|----------|
| 简单分类/提取 | Haiku | 1x |
| 常规问答/写作 | Sonnet | 5x |
| 复杂推理/代码 | Opus | 25x |

用一个"路由器 Agent"先判断任务复杂度，再分发给不同模型。

### 3. 输出长度限制

合理设置 `max_tokens`，避免模型"话痨"：

```typescript
// 只需要 yes/no 的场景
max_tokens: 10

// 摘要生成
max_tokens: 200

// 代码生成
max_tokens: 2048
```

### 4. 批量处理（Batch API）

非实时任务使用 Batch API，价格更低（通常 5 折），24 小时内返回结果：

```typescript
const batch = await anthropic.beta.messages.batches.create({
  requests: questions.map((q, i) => ({
    custom_id: `q-${i}`,
    params: {
      model: 'claude-sonnet-4-6-20251022',
      max_tokens: 1024,
      messages: [{ role: 'user', content: q }],
    },
  })),
});
```

---

## 性能优化策略

### 1. 流式输出（Streaming）

首 Token 延迟（Time to First Token）是用户最敏感的指标。流式输出让用户在模型生成第一个 Token 时就看到响应，而不是等全部生成完。

```typescript
const stream = await anthropic.messages.create({
  ...params,
  stream: true,
});

for await (const chunk of stream) {
  if (chunk.type === 'content_block_delta') {
    res.write(chunk.delta.text); // 实时推送给前端
  }
}
```

### 2. 预加载与预热

对于高频场景，可以预先生成常见问题的回答：

```typescript
// 启动时预热
const hotQuestions = ['你们支持退款吗？', '怎么联系客服？'];
for (const q of hotQuestions) {
  const answer = await llm.ask(q);
  await redis.setex(`faq:${hash(q)}`, 3600, answer);
}

// 查询时先读缓存
const cached = await redis.get(`faq:${hash(question)}`);
if (cached) return cached;
```

### 3. 并发控制

LLM API 有速率限制（RPM/TPM），并发过高会触发限流：

```typescript
import pLimit from 'p-limit';

const limit = pLimit(5); // 最多 5 个并发

const results = await Promise.all(
  requests.map(req => limit(() => callLLM(req)))
);
```

### 4. 长上下文优化

上下文窗口越长，处理越慢、成本越高：

- **滑动窗口**：只保留最近 N 轮对话
- **摘要压缩**：历史对话超过阈值时，让 LLM 生成摘要替代原文
- **RAG 替代**：用检索代替把全部文档塞进 Prompt

---

## 监控与告警

建立成本监控体系：

```typescript
// 每次请求记录
await db.insert('llm_logs', {
  model: params.model,
  input_tokens: usage.input_tokens,
  output_tokens: usage.output_tokens,
  cost_usd: calculateCost(usage),
  latency_ms: Date.now() - startTime,
  timestamp: new Date(),
});

// 每日报警：单日成本超过 $50 发通知
```

---

## 写在最后

AI 应用的成本控制是一门平衡艺术：

- **不要为了省钱牺牲用户体验**（比如用太弱的模型导致回答质量差）
- **不要为了性能过度工程**（比如对低频任务做复杂缓存）
- **持续监控，数据驱动决策**

建议每月做一次成本审计，分析 Token 消耗分布，找到优化空间。

---

**专栏结语**

这 7 篇文章从 AI Agent 基础概念出发，依次覆盖了 MCP 协议、Prompt Engineering、Claude API 实战、RAG 检索增强、工作流编排和成本优化。希望这个系列能帮助你从零开始构建可靠的 AI 应用。

如果你在实际落地中遇到问题，欢迎在评论区留言交流。
